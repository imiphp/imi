<?php
namespace Imi\Server\WebSocket;

use Imi\App;
use Imi\Util\Bit;
use Imi\Event\Event;
use Imi\Server\Base;
use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Util\ImiPriority;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Http\Listener\BeforeRequest;
use Imi\Server\Event\Param\MessageEventParam;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Param\HandShakeEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;

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
     * 是否为 https 服务
     *
     * @var bool
     */
    private $https;

    /**
     * 是否为 http2 服务
     *
     * @var bool
     */
    private $http2;

    /**
     * 创建 swoole 服务器对象
     * @return void
     */
    protected function createServer()
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\WebSocket\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->https = $this->wss = defined('SWOOLE_SSL') && Bit::has($config['sockType'], SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
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
        $thisConfig = &$this->config;
        if(!isset($thisConfig['configs']['open_websocket_protocol']))
        {
            $thisConfig['configs']['open_websocket_protocol'] = true;
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
        Event::one('IMI.MAIN_SERVER.WORKER.START.APP', function(WorkerStartEventParam $e){
            // 内置事件监听
            $this->on('request', [new BeforeRequest, 'handle'], ImiPriority::IMI_MAX);
        });

        $events = $this->config['events'] ?? null;
        if($event = ($events['handshake'] ?? true))
        {
            $this->swoolePort->on('handshake', is_callable($event) ? $event : function(\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse){
                try{
                    RequestContext::muiltiSet([
                        'server'        =>  $this,
                        'swooleRequest' =>  $swooleRequest,
                        'swooleResponse'=>  $swooleResponse,
                    ]);
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

        if($event = ($events['message'] ?? true))
        {
            $this->swoolePort->on('message', is_callable($event) ? $event : function ($server, \Swoole\WebSocket\Frame $frame) {
                try{
                    RequestContext::muiltiSet([
                        'server'        =>  $this,
                    ]);
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

        if($event = ($events['close'] ?? true))
        {
            $this->swoolePort->on('close', is_callable($event) ? $event : function($server, $fd, $reactorID){
                try{
                    RequestContext::muiltiSet([
                        'server'        =>  $this,
                    ]);
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

        if($event = ($events['request'] ?? true))
        {
            $this->swoolePort->on('request', is_callable($event) ? $event : function(\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse){
                try{
                    RequestContext::muiltiSet([
                        'server'        =>  $this,
                        'swooleRequest' =>  $swooleRequest,
                        'swooleResponse'=>  $swooleResponse,
                    ]);
                    $this->trigger('request', [
                        'request'   => Request::getInstance($this, $swooleRequest),
                        'response'  => Response::getInstance($this, $swooleResponse),
                    ], $this, RequestEventParam::class);
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

    /**
     * 是否为 https 服务
     *
     * @return bool
     */ 
    public function isHttps()
    {
        return $this->https;
    }

    /**
     * 是否为 http2 服务
     *
     * @return bool
     */ 
    public function isHttp2()
    {
        return $this->http2;
    }
}