<?php
namespace Imi\Server\Http;

use Imi\App;
use Imi\Server\Base;
use Imi\ServerManage;
use Imi\Util\ImiPriority;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Http\Listener\BeforeRequest;
use Imi\Server\Event\Param\RequestEventParam;

/**
 * Http 服务器类
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
        $this->swooleServer = new \Swoole\Http\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
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
    }

    /**
     * 获取服务器初始化需要的配置
     * @return array
     */
    protected function getServerInitConfig()
    {
        return [
            'host'      => isset($this->config['host']) ? $this->config['host'] : '0.0.0.0',
            'port'      => isset($this->config['port']) ? $this->config['port'] : 80,
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
        // 内置事件监听
        $this->on('request', [new BeforeRequest, 'handle'], ImiPriority::IMI_MAX);

        // Swoole 服务器对象事件监听
        $server = $this->swoolePort ?? $this->swooleServer;

        if($event = ($this->config['events']['request'] ?? true))
        {
            $server->on('request', is_callable($event) ? $event : function(\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse){
                try{
                    $request = Request::getInstance($this, $swooleRequest);
                    $response = Response::getInstance($this, $swooleResponse);
                    $this->trigger('request', [
                        'request'   => $request,
                        'response'  => $response,
                    ], $this, RequestEventParam::class);
                }
                catch(\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }

        if($event = ($this->config['events']['close'] ?? false))
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
}