<?php
namespace Imi\Tool\Tools\Server;

use Imi\App;
use Imi\Util\Imi;
use Imi\Util\File;
use Imi\ServerManage;
use Imi\Tool\ArgType;
use Swoole\Coroutine;
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
     * 
     * @return void
     */
    public function start()
    {
        Imi::buildRuntime(Imi::getRuntimePath('imi-runtime-bak.cache'));
        RequestContext::destroy();
        PoolManager::clearPools();
        CacheManager::clearPools();
        App::createServers();
        ServerManage::getServer('main')->getSwooleServer()->start();
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
        echo 'code:', $result['result']['code'], ', signal:', $result['result']['signal'], ', output:', $result['result']['output'], PHP_EOL;
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
        echo 'code:', $result['result']['code'], ', signal:', $result['result']['signal'], ', output:', $result['result']['output'], PHP_EOL;
    }
}