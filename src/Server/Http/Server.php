<?php

namespace Imi\Server\Http;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Base;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Http\Listener\BeforeRequest;
use Imi\Server\Http\Listener\Http2AfterClose;
use Imi\Server\Http\Listener\Http2BeforeClose;
use Imi\ServerManage;
use Imi\Swoole\Http\Message\SwooleRequest;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Util\Bit;
use Imi\Util\ImiPriority;
use Swoole\Http\Server as HttpServer;

/**
 * Http 服务器类.
 *
 * @Bean
 */
class Server extends Base
{
    /**
     * 是否为 https 服务
     *
     * @var bool
     */
    private bool $https;

    /**
     * 是否为 http2 服务
     *
     * @var bool
     */
    private bool $http2;

    /**
     * 创建 swoole 服务器对象
     *
     * @return void
     */
    protected function createServer()
    {
        $config = $this->getServerInitConfig();
        if ($config['coServer'])
        {
            $this->swooleServer = new \Co\Http\Server($config['host'], $config['port'], $config['ssl'], $config['reuse_port']);
        }
        else
        {
            $this->swooleServer = new \Swoole\Http\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        }
        $this->https = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * 从主服务器监听端口，作为子服务器.
     *
     * @return void
     */
    protected function createSubServer()
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = ServerManage::getServer('main')->getSwooleServer();
        $this->swoolePort = $this->swooleServer->addListener($config['host'], $config['port'], $config['sockType']);
        $thisConfig = &$this->config;
        if (!isset($thisConfig['configs']['open_http_protocol']))
        {
            $thisConfig['configs']['open_http_protocol'] = true;
        }
        $this->https = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
        $this->http2 = $thisConfig['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * 获取服务器初始化需要的配置.
     *
     * @return array
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'       => isset($this->config['host']) ? $this->config['host'] : '0.0.0.0',
            'port'       => isset($this->config['port']) ? $this->config['port'] : 80,
            'sockType'   => isset($this->config['sockType']) ? $this->config['sockType'] : \SWOOLE_SOCK_TCP,
            'mode'       => isset($this->config['mode']) ? $this->config['mode'] : \SWOOLE_PROCESS,
            'coServer'   => $this->config['coServer'] ?? false,
            'ssl'        => $this->config['ssl'] ?? false,
            'reuse_port' => $this->config['reuse_port'] ?? true,
        ];
    }

    /**
     * 绑定服务器事件.
     *
     * @return void
     */
    protected function bindEvents()
    {
        $config = $this->getServerInitConfig();
        if (!$config['coServer'])
        {
            parent::bindEvents();

            return;
        }
        if ($event = ($this->config['events']['request'] ?? true))
        {
            $this->swooleServer->handle('/', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse) {
                try
                {
                    $request = new SwooleRequest($this, $swooleRequest);
                    $response = new SwooleResponse($this, $swooleResponse);
                    RequestContext::muiltiSet([
                        'server'         => $this,
                        'swooleRequest'  => $swooleRequest,
                        'swooleResponse' => $swooleResponse,
                        'request'        => $request,
                        'response'       => $response,
                    ]);
                    $this->trigger('request', [
                        'request'   => $request,
                        'response'  => $response,
                    ], $this, RequestEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        $this->__bindEvents();
    }

    /**
     * 绑定服务器事件.
     *
     * @return void
     */
    protected function __bindEvents()
    {
        $config = $this->getServerInitConfig();

        Event::one('IMI.MAIN_SERVER.WORKER.START.APP', function (WorkerStartEventParam $e) {
            // 内置事件监听
            $this->on('request', [new BeforeRequest($this), 'handle'], ImiPriority::IMI_MAX);
            if ($this->http2)
            {
                $this->on('close', [new Http2BeforeClose(), 'handle'], ImiPriority::IMI_MAX);
                $this->on('close', [new Http2AfterClose(), 'handle'], ImiPriority::IMI_MIN);
            }
        });

        if ($config['coServer'])
        {
            return;
        }

        // Swoole 服务器对象事件监听

        $events = $this->config['events'] ?? null;
        if ($event = ($events['request'] ?? true))
        {
            $this->swoolePort->on('request', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse) {
                try
                {
                    $request = new SwooleRequest($this, $swooleRequest);
                    $response = new SwooleResponse($this, $swooleResponse);
                    RequestContext::muiltiSet([
                        'server'         => $this,
                        'swooleRequest'  => $swooleRequest,
                        'swooleResponse' => $swooleResponse,
                        'request'        => $request,
                        'response'       => $response,
                    ]);
                    $this->trigger('request', [
                        'request'  => $request,
                        'response' => $response,
                    ], $this, RequestEventParam::class);
                }
                catch (\Throwable $th)
                {
                    App::getBean('ErrorLog')->onException($th);
                    if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                    {
                        throw $th;
                    }
                }
            });
        }

        if ($event = ($events['close'] ?? false) || $this->http2)
        {
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (HttpServer $server, int $fd, int $reactorId) {
                try
                {
                    $this->trigger('close', [
                        'server'    => $this,
                        'fd'        => $fd,
                        'reactorId' => $reactorId,
                    ], $this, CloseEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
    }

    /**
     * 是否为 https 服务
     *
     * @return bool
     */
    public function isSSL(): bool
    {
        return $this->https;
    }

    /**
     * 是否为 http2 服务
     *
     * @var bool
     */
    public function isHttp2()
    {
        return $this->http2;
    }

    /**
     * 是否为长连接服务
     *
     * @return bool
     */
    public function isLongConnection(): bool
    {
        return $this->isHttp2();
    }
}
