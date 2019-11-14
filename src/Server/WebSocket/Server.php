<?php
namespace Imi\Server\WebSocket;

use Imi\App;
use Imi\Util\Bit;
use Imi\Server\Base;
use Imi\ServerManage;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Param\MessageEventParam;
use Imi\Server\Event\Param\HandShakeEventParam;

/**
 * WebSocket 服务器类
 * @Bean
 */
class Server extends Base
{
    /**
     * 是否为 wss 服务
     *
     * @var bool
     */
    private $wss;

    /**
     * 创建 swoole 服务器对象
     * @return void
     */
    protected function createServer()
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\WebSocket\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->wss = defined('SWOOLE_SSL') && Bit::has($config['sockType'], SWOOLE_SSL);
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
        if(!isset($this->config['configs']['open_websocket_protocol']))
        {
            $this->config['configs']['open_websocket_protocol'] = true;
        }
        $this->wss = defined('SWOOLE_SSL') && Bit::has($config['sockType'], SWOOLE_SSL);
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
            'sockType'  => isset($this->config['sockType']) ? $this->config['sockType'] : SWOOLE_SOCK_TCP,
            'mode'      => isset($this->config['mode']) ? $this->config['mode'] : SWOOLE_PROCESS,
        ];
    }

    /**
     * 绑定服务器事件
     * @return void
     */
    protected function __bindEvents()
    {
        $server = $this->swoolePort ?? $this->swooleServer;

        if($event = ($this->config['events']['handshake'] ?? true))
        {
            $server->on('handshake', is_callable($event) ? $event : function(\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse){
                try{
                    $this->trigger('handShake', [
                        'request'   => new Request($this, $swooleRequest),
                        'response'  => new Response($this, $swooleResponse),
                    ], $this, HandShakeEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }

        if($event = ($this->config['events']['message'] ?? true))
        {
            $server->on('message', is_callable($event) ? $event : function (\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {
                try{
                    $this->trigger('message', [
                        'server'    => $this,
                        'frame'     => $frame,
                    ], $this, MessageEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }

        if($event = ($this->config['events']['close'] ?? true))
        {
            $server->on('close', is_callable($event) ? $event : function(\Swoole\Http\Server $server, $fd, $reactorID){
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

    /**
     * 是否为 wss 服务
     *
     * @return boolean
     */
    public function isSSL()
    {
        return $this->wss;
    }

}