<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Cli;

use Imi\App;
use Imi\Cache\CacheManager;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\CliApp;
use Imi\Cli\Contract\BaseCommand;
use Imi\Cli\ImiCommand;
use Imi\Config;
use Imi\Event\Event;
use Imi\Pool\PoolManager;
use Imi\Server\ServerManager;
use Imi\Worker as ImiWorker;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Server as WorkermanServerUtil;
use Imi\Workerman\Server\WorkermanServerWorker;
use Workerman\Worker;

/**
 * @Command("workerman")
 */
class Server extends BaseCommand
{
    /**
     * 开启服务
     *
     * @CommandAction(name="start", description="启动 workerman 服务")
     * @Option(name="name", type=ArgType::STRING, required=false, comments="要启动的服务器名")
     * @Option(name="workerNum", type=ArgType::INT, required=false, comments="工作进程数量")
     * @Option(name="daemon", shortcut="d", type=ArgType::BOOL, required=false, default=false, comments="是否启用守护进程模式。加 -d 参数则使用守护进程模式")
     */
    public function start(?string $name, ?int $workerNum, bool $d = false): void
    {
        $this->outStartupInfo();
        if (Config::get('@app.server.checkPoolResource', false) && !PoolManager::checkPoolResource())
        {
            exit(255);
        }
        PoolManager::clearPools();
        CacheManager::clearPools();

        // workerman argv
        global $argv;
        $argv = [
            $argv[0],
            'start',
        ];
        unset($argv);

        // 守护进程
        WorkermanServerWorker::$daemonize = $d;

        Event::trigger('IMI.WORKERMAN.SERVER.BEFORE_START');
        // 创建服务器对象们前置操作
        Event::trigger('IMI.SERVERS.CREATE.BEFORE');
        $serverConfigs = Config::get('@app.workermanServer', []);
        $output = ImiCommand::getOutput();
        if (null === $name)
        {
            $shares = [];
            foreach ($serverConfigs as $serverName => $config)
            {
                if (!($config['autorun'] ?? true))
                {
                    continue;
                }
                $shareWorker = $config['shareWorker'] ?? false;
                // 这边共享 Worker 的服务只创建一次
                if (false === $shareWorker)
                {
                    /** @var IWorkermanServer $server */
                    $server = ServerManager::createServer($serverName, $config);
                    if ($workerNum > 0)
                    {
                        $server->getWorker()->count = $workerNum;
                    }
                    $server->parseConfig($config);
                    $output->writeln('<info>[' . $config['type'] . ']</info> <comment>' . $serverName . '</comment>; <info>listen:</info> ' . $config['socketName']);
                }
                else
                {
                    $shares[$serverName] = $config;
                }
            }
            if ($shares)
            {
                foreach ($shares as $serverName => $config)
                {
                    /** @var IWorkermanServer $server */
                    $server = ServerManager::getServer($config['shareWorker']);
                    $server->parseConfig($config);
                    $output->writeln('<info>[' . $config['type'] . ']</info> <comment>' . $serverName . '</comment>; <info>shareWorker:</info> ' . $config['shareWorker'] . '; <info>listen:</info> ' . $config['socketName']);
                }
            }
        }
        elseif (!isset($serverConfigs[$name]))
        {
            throw new \RuntimeException(sprintf('Server [%s] not found', $name));
        }
        else
        {
            $config = $serverConfigs[$name];
            /** @var IWorkermanServer $server */
            $server = ServerManager::createServer($name, $config);
            if ($workerNum > 0)
            {
                $server->getWorker()->count = $workerNum;
            }
            $server->parseConfig($config);
            $output->writeln('<info>[' . $config['type'] . ']</info> <comment>' . $name . '</comment>; <info>listen:</info> ' . $config['socketName']);
        }
        // @phpstan-ignore-next-line
        ImiWorker::setWorkerHandler(App::getBean('WorkermanWorkerHandler'));
        // 创建服务器对象们后置操作
        Event::trigger('IMI.SERVERS.CREATE.AFTER');
        WorkermanServerUtil::initWorkermanWorker($name);
        Event::trigger('IMI.APP.INIT', [], $this);
        // gc
        gc_collect_cycles();
        gc_mem_caches();
        // 启动服务
        WorkermanServerWorker::runAll();
    }

    /**
     * 停止服务
     *
     * @CommandAction(name="stop", description="停止 workerman 服务")
     */
    public function stop(): void
    {
        WorkermanServerUtil::initWorkermanWorker();
        // workerman argv
        global $argv;
        $argv = [
            $argv[0],
            'stop',
        ];
        unset($argv);

        WorkermanServerWorker::runAll();
    }

    /**
     * 输出启动信息.
     */
    public function outStartupInfo(): void
    {
        CliApp::printImi();
        CliApp::printEnvInfo('Workerman', WorkermanServerWorker::VERSION);
    }
}
