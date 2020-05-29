<?php
namespace Imi\Server\TcpServer;

use Imi\App;
use Imi\Server\Base;
use Imi\ServerManage;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Param\ConnectEventParam;
use Imi\Server\Event\Param\ReceiveEventParam;

/**
 * TCP 服务器类
 * @Bean
 */
class Server extends Base
{
    /**
     * 创建 swoole 服务器对象
     * @return void
     */
    protected function createServer()
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
    }

    /**
     * 从主服务器监听端口，作为子服务器
     * @return void
     */
    protected function createSubServer()
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = ServerManage::getServer('main')->getSwooleServer();
        $this->swoolePort = $this->swooleServer->addListener($config['host'], $config['port'], $config['sockType']);
        $configs = &$this->config['configs'];
        foreach(static::SWOOLE_PROTOCOLS as $protocol)
        {
            if(!isset($configs[$protocol]))
            {
                $configs[$protocol] = false;
            }
        }
    }

    /**
     * 获取服务器初始化需要的配置
     * @return array
     */
    protected function getServerInitConfig()
    {
        return [
            'host'      => isset($this->config['host']) ? $this->config['host'] : '0.0.0.0',
            'port'      => isset($this->config['port']) ? $this->config['port'] : 8080,
            'sockType'  => isset($this->config['sockType']) ? (SWOOLE_SOCK_TCP | $this->config['sockType']) : SWOOLE_SOCK_TCP,
            'mode'      => isset($this->config['mode']) ? $this->config['mode'] : SWOOLE_PROCESS,
        ];
    }

    /**
     * 绑定服务器事件
     * @return void
     */
    protected function __bindEvents()
    {
        $events = $this->config['events'] ?? null;
        if($event = ($events['connect'] ?? true))
        {
            $this->swoolePort->on('connect', is_callable($event) ? $event : function(\Swoole\Server $server, $fd, $reactorID){
                try{
                    $this->trigger('connect', [
                        'server'    => $this,
                        'fd'        => $fd,
                        'reactorID' => $reactorID,
                    ], $this, ConnectEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        
        if($event = ($events['receive'] ?? true))
        {
            $this->swoolePort->on('receive', is_callable($event) ? $event : function(\Swoole\Server $server, $fd, $reactorID, $data){
                try{
                    $this->trigger('receive', [
                        'server'    => $this,
                        'fd'        => $fd,
                        'reactorID' => $reactorID,
                        'data'      => $data,
                    ], $this, ReceiveEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
    
        if($event = ($events['close'] ?? true))
        {
            $this->swoolePort->on('close', is_callable($event) ? $event : function(\Swoole\Server $server, $fd, $reactorID){
                try{
                    $this->trigger('close', [
                        'server'    => $this,
                        'fd'        => $fd,
                        'reactorID' => $reactorID,
                    ], $this, CloseEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }

    }
}