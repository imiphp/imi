<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\App;
use Imi\Bean\Annotation;
use Imi\Bean\Scanner;
use Imi\Cli\CliApp;
use Imi\Cli\ImiCommand;
use Imi\Config;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Main\Helper;
use function Imi\ttyExec;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class WorkermanApp extends CliApp
{
    /**
     * 应用模式的配置.
     */
    protected array $appConfig = [
        'Timer' => WorkermanTimer::class,
        'beans' => [
            'ServerUtil' => \Imi\Workerman\Server\Util\LocalServerUtil::class,
        ],
    ];

    /**
     * 构造方法.
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
     * 获取应用类型.
     */
    public function getType(): string
    {
        return 'workerman';
    }

    protected function __loadConfig(): void
    {
        parent::__loadConfig();

        foreach (Config::get('@app.workermanServer', []) as $name => $config)
        {
            // 加载服务器配置文件
            foreach (Imi::getNamespacePaths($config['namespace']) as $path)
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
     * 加载运行时.
     */
    public function loadRuntime(): int
    {
        $this->initRuntime();
        $input = ImiCommand::getInput();
        $isServerStart = ('workerman/start' === ($_SERVER['argv'][1] ?? null));
        if (!$isServerStart)
        {
            // 尝试加载项目运行时
            $appRuntimeFile = $input->getParameterOption('--app-runtime');
            if (false !== $appRuntimeFile && Imi::loadRuntimeInfo($appRuntimeFile))
            {
                return LoadRuntimeResult::ALL;
            }
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
            $result = Imi::loadRuntimeInfo(Imi::getCurrentModeRuntimePath('imi-runtime'), true);
        }
        if (!$result)
        {
            // 不使用缓存时去扫描
            Scanner::scanImi();
        }
        if ($isServerStart)
        {
            $imiRuntime = Imi::getCurrentModeRuntimePath('imi-runtime');
            Imi::buildRuntime($imiRuntime);
            $success = false;
            if (\extension_loaded('pcntl'))
            {
                $pid = pcntl_fork();
                if ($pid)
                {
                    pcntl_wait($status);
                    if (0 === $status)
                    {
                        $success = true;
                    }
                }
                elseif (0 === $pid)
                {
                    // 子进程
                    Scanner::scanVendor();
                    Scanner::scanApp();
                    Imi::buildRuntime();
                    exit;
                }
            }

            if (!$success)
            {
                // 执行命令行生成缓存
                $cmd = Imi::getImiCmd('imi/buildRuntime', [], [
                    'imi-runtime' => $imiRuntime,
                ]);
                $code = ttyExec(\Imi\cmd($cmd));
                if (0 !== $code)
                {
                    exit($code);
                }
            }
            $result = Imi::loadRuntimeInfo(Imi::getCurrentModeRuntimePath('runtime'));

            return LoadRuntimeResult::ALL;
        }

        return LoadRuntimeResult::IMI_LOADED;
    }

    /**
     * 加载入口.
     */
    public function loadMain(): void
    {
        parent::loadMain();
        foreach (Config::get('@app.workermanServer', []) as $name => $config)
        {
            Helper::getMain($config['namespace'], 'server.' . $name);
        }
    }

    private function onCommand(ConsoleCommandEvent $e): void
    {
        App::set(ProcessAppContexts::MASTER_PID, getmypid(), true);
    }

    private function onScanApp(): void
    {
        $namespaces = [];
        foreach (Config::get('@app.workermanServer', []) as $config)
        {
            $namespaces[] = $config['namespace'];
        }
        Annotation::getInstance()->initByNamespace($namespaces);
    }
}
