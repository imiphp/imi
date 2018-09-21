<?php
namespace Imi\Tool\Tools\Server;

use Imi\App;
use Imi\ServerManage;
use Imi\Tool\ArgType;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;
use Swoole\Coroutine;
use Imi\Util\File;
use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;

/**
 * @Tool("server")
 */
class Server
{
    /**
     * 开启服务
     * 
     * @Operation("start")
     * 
     * @return void
     */
    public function start()
    {
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
        go(function(){
            $fileName = File::path(dirname($_SERVER['SCRIPT_NAME']), str_replace('\\', '-', App::getNamespace()) . '.pid');
            if(!is_file($fileName))
            {
                exit(sprintf('pid file %s is not exists', $fileName));
            }
            $pid = json_decode(File::readFile($fileName), true);
            if($pid > 0)
            {
                $cmd = 'kill ' . $pid['masterPID'];
                echo $cmd, PHP_EOL;
                $result = Coroutine::exec($cmd);
                echo 'code:', $result['code'], ', signal:', $result['signal'], ', output:', $result['output'], PHP_EOL;
            }
            else
            {
                echo 'pid does not exists!', PHP_EOL;
            }
        });
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
        go(function(){
            $fileName = File::path(dirname($_SERVER['SCRIPT_NAME']), str_replace('\\', '-', App::getNamespace()) . '.pid');
            if(!is_file($fileName))
            {
                exit(sprintf('pid file %s is not exists', $fileName));
            }
            $pid = json_decode(File::readFile($fileName), true);
            if($pid > 0)
            {
                $cmd = 'kill -USR1 ' . $pid['managerPID'];
                echo $cmd, PHP_EOL;
                $result = Coroutine::exec($cmd);
                echo 'code:', $result['code'], ', signal:', $result['signal'], ', output:', $result['output'], PHP_EOL;
            }
            else
            {
                echo 'pid does not exists!', PHP_EOL;
            }
        });
    }
}