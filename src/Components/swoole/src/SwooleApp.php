<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\App;
use Imi\Bean\Annotation;
use Imi\Bean\BeanManager;
use Imi\Bean\Scanner;
use Imi\Cache\CacheManager;
use Imi\Cli\CliApp;
use Imi\Cli\ImiCommand;
use Imi\Config;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Lock\Lock;
use Imi\Main\Helper;
use Imi\Pool\PoolManager;
use Imi\Swoole\Context\CoroutineContextManager;
use Imi\Swoole\Util\AtomicManager;
use function Imi\ttyExec;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Worker;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\OutputInterface;

class SwooleApp extends CliApp
{
    /**
     * {@inheritDoc}
     */
    protected array $appConfig = [
        'RequestContext' => CoroutineContextManager::class,
        'Timer'          => SwooleTimer::class,
        'Async'          => \Imi\Swoole\Async\SwooleHandler::class,
        'beans'          => [
            'ServerUtil' => \Imi\Swoole\Server\Util\LocalServerUtil::class,
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        $this->cliEventDispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $e) {
            $this->onCommand($e);
        }, \PHP_INT_MAX - 1000);
        Event::one('IMI.SCAN_APP', function () {
            $this->onScanApp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'swoole';
    }

    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        try
        {
            $this->cli->run(ImiCommand::getInput(), ImiCommand::getOutput());
        }
        catch (\Swoole\ExitException $e)
        {
            throw $e;
        }
        catch (\Exception $th)
        {
            /** @var \Imi\Log\ErrorLog $errorLog */
            $errorLog = App::getBean('ErrorLog');
            $errorLog->onException($th);
            exit(255);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function __loadConfig(): void
    {
        parent::__loadConfig();

        $namespace = Config::get('@app.mainServer.namespace');
        $namespaces = [];
        if (null !== $namespace)
        {
            $namespaces['main'] = $namespace;
        }
        foreach (Config::get('@app.subServers', []) as $name => $config)
        {
            $namespaces[$name] = $config['namespace'];
        }
        foreach ($namespaces as $name => $namespace)
        {
            // 加载服务器配置文件
            foreach (Imi::getNamespacePaths($namespace) as $path)
            {
                $fileName = $path . '/config/config.php';
                if (is_file($fileName))
                {
                    Config::addConfig('@server.' . $name, include $fileName);
                    break;
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadRuntime(): int
    {
        $this->initRuntime();
        $input = ImiCommand::getInput();
        // 尝试加载项目运行时
        $appRuntimeFile = $input->getParameterOption('--app-runtime');
        if (false !== $appRuntimeFile && Imi::loadRuntimeInfo($appRuntimeFile))
        {
            return LoadRuntimeResult::ALL;
        }
        // 尝试加载 imi 框架运行时
        if ($file = $input->getParameterOption('--imi-runtime'))
        {
            // 尝试加载指定 runtime
            $result = Imi::loadRuntimeInfo($file, true);
        }
        else
        {
            // 尝试加载默认 runtime
            $result = Imi::loadRuntimeInfo($imiRuntime = Imi::getCurrentModeRuntimePath('imi-runtime'), true);
        }
        if (!$result)
        {
            // 不使用缓存时去扫描
            Scanner::scanImi();
        }
        if ('swoole/start' === ($_SERVER['argv'][1] ?? null))
        {
            $imiRuntime ??= Imi::getCurrentModeRuntimePath('imi-runtime');
            Imi::buildRuntime($imiRuntime);

            // 执行命令行生成缓存
            $cmd = Imi::getImiCmd('imi/buildRuntime', [], [
                'imi-runtime' => $imiRuntime,
            ]);
            $code = ttyExec(\Imi\cmd($cmd));
            if (0 !== $code)
            {
                exit($code);
            }
            $result = Imi::loadRuntimeInfo(Imi::getCurrentModeRuntimePath('runtime'));

            return LoadRuntimeResult::ALL;
        }

        return LoadRuntimeResult::IMI_LOADED;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMain(): void
    {
        parent::loadMain();
        // 服务器们
        $servers = array_merge(['main' => Config::get('@app.mainServer')], Config::get('@app.subServers', []));
        foreach ($servers as $serverName => $item)
        {
            if ($item)
            {
                Helper::getMain($item['namespace'], 'server.' . $serverName);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();
        register_shutdown_function(static function () {
            // @phpstan-ignore-next-line
            App::getBean('Logger')->clear();
        });
        foreach (Config::getAliases() as $alias)
        {
            // 原子计数初始化
            AtomicManager::setNames(Config::get($alias . '.atomics', []));
        }
        AtomicManager::init();
        if (BeanManager::get('SwooleWorkerHandler'))
        {
            // @phpstan-ignore-next-line
            Worker::setWorkerHandler(App::getBean('SwooleWorkerHandler'));
        }
        Event::one(['IMI.PROCESS.BEGIN', 'IMI.MAIN_SERVER.WORKER.START'], static function () {
            PoolManager::init();
            CacheManager::init();
            Lock::init();
        });
    }

    private function onCommand(ConsoleCommandEvent $e): void
    {
        $this->checkEnvironment($e->getOutput());
        App::set(ProcessAppContexts::PROCESS_NAME, ProcessType::MASTER, true);
        App::set(ProcessAppContexts::MASTER_PID, getmypid(), true);
    }

    private function onScanApp(): void
    {
        $namespace = Config::get('@app.mainServer.namespace');
        $namespaces = [];
        if (null !== $namespace)
        {
            $namespaces[] = $namespace;
        }
        foreach (Config::get('@app.subServers', []) as $config)
        {
            $namespaces[] = $config['namespace'];
        }
        Annotation::getInstance()->initByNamespace($namespaces);
    }

    /**
     * 检查环境.
     */
    private function checkEnvironment(OutputInterface $output): void
    {
        // Swoole 检查
        if (!\extension_loaded('swoole'))
        {
            $output->writeln('<error>Swoole extension must be installed!</error>');
            $output->writeln('<info>Swoole Github:</info> <comment>https://github.com/swoole/swoole-src</comment>');
            exit(255);
        }
    }
}
