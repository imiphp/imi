<?php

namespace Imi\Tool\Tools\Server;

use Imi\App;
use Imi\Cache\CacheManager;
use Imi\Pool\PoolManager;
use Imi\ServerManage;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;
use Imi\Tool\Tools\Imi\Imi as ToolImi;
use Imi\Util\Imi;

/**
 * @Tool("server")
 */
class Server
{
    /**
     * 开启服务
     *
     * @Operation(name="start", co=false)
     * @Arg(name="name", type=ArgType::STRING, required=false, comments="要启动的服务器名")
     * @Arg(name="workerNum", type=ArgType::INT, required=false, comments="工作进程数量")
     * @Arg(name="d", type=ArgType::STRING, required=false, comments="是否启用守护进程模式。加 -d 参数则使用守护进程模式。如果后面再跟上文件名，则会把标准输入和输出重定向到该文件")
     *
     * @param string|null $name
     * @param int|null    $workerNum
     * @param bool|string $d
     *
     * @return void
     */
    public function start($name, $workerNum, $d)
    {
        PoolManager::clearPools();
        CacheManager::clearPools();
        if (null === $name)
        {
            App::createServers();
            $swooleServer = ServerManage::getServer('main')->getSwooleServer();
            // 守护进程支持
            if ($d)
            {
                $options = [
                    'daemonize' => 1,
                ];
                if (true !== $d)
                {
                    $options['log_file'] = $d;
                }
                $swooleServer->set($options);
            }
            $swooleServer->start();
        }
        else
        {
            $server = App::createCoServer($name, $workerNum);
            $server->run();
        }
    }

    /**
     * 停止服务
     *
     * @Operation("stop")
     *
     * @return void
     */
    public function stop()
    {
        Imi::stopServer();
    }

    /**
     * 重新加载服务
     *
     * 重启 Worker 进程，不会导致连接断开，可以让项目文件更改生效
     *
     * @Operation("reload")
     * @Arg(name="runtime", type=ArgType::BOOL, required=false, default=false, comments="是否更新运行时缓存")
     *
     * @param bool $runtime
     *
     * @return void
     */
    public function reload($runtime)
    {
        if ($runtime)
        {
            $imi = new ToolImi();
            echo 'Building runtime...', \PHP_EOL;
            $time = microtime(true);
            $imi->buildRuntime('', null, false, false);
            $useTime = microtime(true) - $time;
            echo 'Runtime build complete! ', $useTime, 's', \PHP_EOL;
        }
        Imi::reloadServer();
    }
}
