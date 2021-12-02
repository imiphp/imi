<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Cli;

use Imi\App;
use Imi\Cache\CacheManager;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Config;
use Imi\Event\Event;
use Imi\Pool\PoolManager;
use Imi\Server\ServerManager;
use Imi\Util\Imi;
use Imi\Util\System;
use Imi\Worker as ImiWorker;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Server as WorkermanServerUtil;
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
        $this->outImi();
        $this->outStartupInfo();
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
        Worker::$daemonize = $d;

        Event::trigger('IMI.WORKERMAN.SERVER.BEFORE_START');
        // 创建服务器对象们前置操作
        Event::trigger('IMI.SERVERS.CREATE.BEFORE');
        $serverConfigs = Config::get('@app.workermanServer');
        if (null === $name)
        {
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
                }
            }
        }
        elseif (!isset($serverConfigs[$name]))
        {
            throw new \RuntimeException(sprintf('Server [%s] not found', $name));
        }
        else
        {
            /** @var IWorkermanServer $server */
            $server = ServerManager::createServer($name, $serverConfigs[$name]);
            if ($workerNum > 0)
            {
                $server->getWorker()->count = $workerNum;
            }
        }
        // @phpstan-ignore-next-line
        ImiWorker::setWorkerHandler(App::getBean('WorkermanWorkerHandler'));
        // 创建服务器对象们后置操作
        Event::trigger('IMI.SERVERS.CREATE.AFTER');
        WorkermanServerUtil::initWorkermanWorker($name);
        Event::trigger('IMI.APP.INIT', [], $this);
        Worker::runAll();
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

        Worker::runAll();
    }

    /**
     * 输出 imi 图标.
     */
    public function outImi(): void
    {
        $this->output->write('<comment>' . <<<'STR'
         _               _
        (_)  _ __ ___   (_)
        | | | '_ ` _ \  | |
        | | | | | | | | | |
        |_| |_| |_| |_| |_|

        </comment>
        STR
        );
    }

    /**
     * 输出启动信息.
     */
    public function outStartupInfo(): void
    {
        $this->output->writeln('<fg=yellow;options=bold>[System]</>');
        $system = (\defined('PHP_OS_FAMILY') && 'Unknown' !== \PHP_OS_FAMILY) ? \PHP_OS_FAMILY : \PHP_OS;
        switch ($system)
        {
            case 'Linux':
                $system .= ' - ' . Imi::getLinuxVersion();
                break;
            case 'Darwin':
                $system .= ' - ' . Imi::getDarwinVersion();
                break;
            case 'CYGWIN':
                $system .= ' - ' . Imi::getCygwinVersion();
                break;
        }
        $this->output->writeln('<info>System:</info> ' . $system);
        if (Imi::isDockerEnvironment())
        {
            $this->output->writeln('<info>Virtual machine:</info> Docker');
        }
        elseif (Imi::isWSL())
        {
            $this->output->writeln('<info>Virtual machine:</info> WSL');
        }
        $this->output->writeln('<info>Disk:</info> Free ' . Imi::formatByte(@disk_free_space('.'), 3) . ' / Total ' . Imi::formatByte(@disk_total_space('.'), 3));

        $netIp = System::netLocalIp();
        if (!empty($netIp)) {
            $this->output->writeln(\PHP_EOL . '<fg=yellow;options=bold>[Network]</>');
            foreach ($netIp as $name => $ip)
            {
                $this->output->writeln('<info>' . $name . '</info>: ' . $ip);
            }
        }

        $this->output->writeln(\PHP_EOL . '<fg=yellow;options=bold>[PHP]</>');
        $this->output->writeln('<info>Version:</info> v' . \PHP_VERSION);
        $this->output->writeln('<info>Workerman:</info> v' . Worker::VERSION);
        $this->output->writeln('<info>imi:</info> ' . App::getImiPrettyVersion());
        $this->output->writeln('<info>Timezone:</info> ' . date_default_timezone_get());

        $this->output->writeln('');
    }
}
