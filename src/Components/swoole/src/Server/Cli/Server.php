<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Cli;

use Imi\Cache\CacheManager;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\CliApp;
use Imi\Cli\Contract\BaseCommand;
use Imi\Cli\Tools\Imi\Imi as ToolImi;
use Imi\Config;
use Imi\Event\Event;
use Imi\Pool\PoolManager;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Util\Imi as SwooleImiUtil;

use function Swoole\Coroutine\run;

/**
 * @Command("swoole")
 */
class Server extends BaseCommand
{
    /**
     * 开启服务
     *
     * @CommandAction(name="start", description="启动 swoole 服务")
     *
     * @Option(name="workerNum", type=ArgType::INT, required=false, comments="工作进程数量")
     * @Option(name="daemon", shortcut="d", type=ArgType::MIXED, required=false, comments="是否启用守护进程模式。加 -d 参数则使用守护进程模式。如果后面再跟上文件名，则会把标准输入和输出重定向到该文件")
     *
     * @param string|bool $d
     */
    public function start(?int $workerNum, $d): void
    {
        Event::one('IMI.SWOOLE.MAIN_COROUTINE.AFTER', function () use ($workerNum, $d): void {
            $server = (function () use ($workerNum, $d) {
                $this->outStartupInfo();
                if (Config::get('@app.server.checkPoolResource', false))
                {
                    $exit = false;
                    run(static function () use (&$exit): void {
                        if (!PoolManager::checkPoolResource())
                        {
                            $exit = true;
                        }
                    });
                    if ($exit)
                    {
                        exit(255);
                    }
                }
                PoolManager::clearPools();
                CacheManager::clearPools();
                Event::trigger('IMI.SWOOLE.SERVER.BEFORE_START');
                // 创建服务器对象们前置操作
                Event::trigger('IMI.SERVERS.CREATE.BEFORE');
                $mainServer = Config::get('@app.mainServer');
                if (null === $mainServer)
                {
                    throw new \RuntimeException('config.mainServer not found');
                }
                // 主服务器
                /** @var ISwooleServer $server */
                $server = ServerManager::createServer('main', $mainServer);
                // 创建监听子服务器端口
                $subServers = Config::get('@app.subServers', []);
                if ($subServers)
                {
                    foreach ($subServers as $name => $config)
                    {
                        ServerManager::createServer($name, $config, true);
                    }
                }
                // 创建服务器对象们后置操作
                Event::trigger('IMI.SERVERS.CREATE.AFTER');

                $swooleServer = $server->getSwooleServer();
                $options = [];
                if (null !== $workerNum)
                {
                    $options['worker_num'] = $workerNum;
                }

                // 守护进程支持
                if ($d)
                {
                    $options['daemonize'] = true;
                    if (true !== $d)
                    {
                        $options['log_file'] = $d;
                    }
                }

                if ($options)
                {
                    $swooleServer->set($options);
                }

                return $server;
            })();
            // gc
            gc_collect_cycles();
            gc_mem_caches();
            // 启动服务
            $server->start();
        });
    }

    /**
     * 停止服务
     *
     * @CommandAction(name="stop", description="停止 swoole 服务")
     */
    public function stop(): void
    {
        SwooleImiUtil::stopServer();
    }

    /**
     * 重新加载服务
     *
     * 重启 Worker 进程，不会导致连接断开，可以让项目文件更改生效
     *
     * @CommandAction(name="reload", description="重载 swoole 服务")
     *
     * @Option(name="runtime", type=ArgType::BOOL, required=false, default=false, comments="是否更新运行时缓存")
     */
    public function reload(bool $runtime): void
    {
        if ($runtime)
        {
            $imi = new ToolImi($this->command, $this->input, $this->output);
            $this->output->writeln('<info>Building runtime...</info>');
            $time = microtime(true);
            $imi->buildRuntime(null, false);
            $useTime = microtime(true) - $time;
            $this->output->writeln("<info>Runtime build complete! {$useTime}s</info>");
        }
        SwooleImiUtil::reloadServer();
    }

    /**
     * 输出启动信息.
     */
    public function outStartupInfo(): void
    {
        CliApp::printImi();
        CliApp::printEnvInfo('Swoole', \SWOOLE_VERSION . (\defined('SWOOLE_CLI') ? ' (swoole-cli)' : ''));
    }
}
