<?php
namespace Imi\Tool\Tools\Server;

use Imi\App;
use Imi\Util\Imi;
use Imi\ServerManage;
use Imi\Tool\ArgType;
use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;

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
     * 
     * @return void
     */
    public function start($name, $workerNum)
    {
        PoolManager::clearPools();
        CacheManager::clearPools();
        if(null === $name)
        {
            App::createServers();
            ServerManage::getServer('main')->getSwooleServer()->start();
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
        $result = Imi::stopServer();
        echo $result['cmd'], PHP_EOL;
    }

    /**
     * 重新加载服务，可以让项目文件更改生效
     * 
     * @Operation("reload")
     * 
     * @return void
     */
    public function reload()
    {
        $result = Imi::reloadServer();
        echo $result['cmd'], PHP_EOL;
    }
}