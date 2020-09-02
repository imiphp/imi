<?php
namespace Imi\Cli\Tools\Server;

use Imi\App;
use Imi\Util\Imi;
use Imi\ServerManage;
use Imi\Cli\ArgType;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Tools\Imi\Imi as ToolImi;

/**
 * @Command("server")
 */
class Server
{
    /**
     * 开启服务
     * 
     * @CommandAction(name="start", co=false)
     * @Option(name="name", type=ArgType::STRING, required=false, comments="要启动的服务器名")
     * @Option(name="workerNum", type=ArgType::INT, required=false, comments="工作进程数量")
     * @Option(name="daemon", shortcut="d", type=ArgType::STRING, required=false, comments="是否启用守护进程模式。加 -d 参数则使用守护进程模式。如果后面再跟上文件名，则会把标准输入和输出重定向到该文件")
     * 
     * @return void
     */
    public function start(?string $name, ?int $workerNum, $d): void
    {
        $this->outImi();
        $this->outStartupInfo();
        PoolManager::clearPools();
        CacheManager::clearPools();
        if(null === $name)
        {
            App::createServers();
            $swooleServer = ServerManage::getServer('main')->getSwooleServer();
            // 守护进程支持
            if($d)
            {
                $options = [
                    'daemonize' =>  1,
                ];
                if(true !== $d)
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
     * @CommandAction("stop")
     * 
     * @return void
     */
    public function stop(): void
    {
        $result = Imi::stopServer();
        echo $result['cmd'], PHP_EOL;
    }

    /**
     * 重新加载服务
     * 
     * 重启 Worker 进程，不会导致连接断开，可以让项目文件更改生效
     * 
     * @CommandAction("reload")
     * @Option(name="runtime", type=ArgType::BOOL, required=false, default=false, comments="是否更新运行时缓存")
     * 
     * @return void
     */
    public function reload(bool $runtime): void
    {
        if($runtime)
        {
            $imi = new ToolImi;
            echo 'Building runtime...', PHP_EOL;
            $time = microtime(true);
            $imi->buildRuntime('', null, false, null);
            $useTime = microtime(true) - $time;
            echo 'Runtime build complete! ', $useTime, 's', PHP_EOL;
        }
        $result = Imi::reloadServer();
        echo $result['cmd'], PHP_EOL;
    }

    /**
     * 输出 imi 图标
     *
     * @return void
     */
    public function outImi(): void
    {
        echo <<<STR
 _               _ 
(_)  _ __ ___   (_)
| | | '_ ` _ \  | |
| | | | | | | | | |
|_| |_| |_| |_| |_|


STR;
    }

    /**
     * 输出启动信息
     *
     * @return void
     */
    public function outStartupInfo(): void
    {
        echo '[System]', PHP_EOL;
        $system = (defined('PHP_OS_FAMILY') && 'Unknown' !== PHP_OS_FAMILY) ? PHP_OS_FAMILY : PHP_OS;
        switch($system)
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
        echo 'System: ', $system, PHP_EOL;
        if(Imi::isDockerEnvironment())
        {
            echo 'Virtual machine: Docker', PHP_EOL;
        }
        else if(Imi::isWSL())
        {
            echo 'Virtual machine: WSL', PHP_EOL;
        }
        echo 'CPU: ', swoole_cpu_num(), ' Cores', PHP_EOL;
        echo 'Disk: Free ', round(@disk_free_space('.') / (1024*1024*1024), 3), ' GB / Total ', round(@disk_total_space('.') / (1024*1024*1024), 3), ' GB', PHP_EOL;

        echo PHP_EOL, '[Network]', PHP_EOL;
        foreach(swoole_get_local_ip() as $name => $ip)
        {
            echo 'ip@', $name, ': ', $ip, PHP_EOL;
        }

        echo PHP_EOL, '[PHP]', PHP_EOL;
        echo 'Version: v', PHP_VERSION, PHP_EOL;
        echo 'Swoole: v', SWOOLE_VERSION, PHP_EOL;
        echo 'imi: ', App::getImiVersion(), PHP_EOL;
        echo 'Timezone: ', date_default_timezone_get(), PHP_EOL;

        echo PHP_EOL;
    }

}