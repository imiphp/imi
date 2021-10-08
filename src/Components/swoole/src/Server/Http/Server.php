<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Server\ServerManager;
use Imi\Swoole\Http\Message\SwooleRequest;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleHttpServer;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Server\Http\Listener\BeforeRequest;
use Imi\Swoole\Server\Http\Listener\Http2AfterClose;
use Imi\Swoole\Server\Http\Listener\Http2BeforeClose;
use Imi\Util\Bit;
use Imi\Util\ImiPriority;
use Swoole\Http\Server as HttpServer;

/**
 * Http 服务器类.
 *
 * @Bean(name="HttpServer", env="swoole")
 */
class Server extends Base implements ISwooleHttpServer
{
    /**
     * 是否为 https 服务
     */
    private bool $https = false;

    /**
     * 是否为 http2 服务
     */
    private bool $http2 = false;

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * 创建 swoole 服务器对象
     */
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\Http\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->https = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * 从主服务器监听端口，作为子服务器.
     */
    protected function createSubServer(): void
    {
        $config = $this->getServerInitConfig();
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);
        $this->swooleServer = $server->getSwooleServer();
        $this->swoolePort = $this->swooleServer->addListener($config['host'], $config['port'], $config['sockType']);
        $thisConfig = &$this->config;
        $thisConfig['configs']['open_http_protocol'] ??= true;
        $this->https = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
        $this->http2 = $thisConfig['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * 获取服务器初始化需要的配置.
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'       => $this->config['host'] ?? '0.0.0.0',
            'port'       => $this->config['port'] ?? 80,
            'sockType'   => $this->config['sockType'] ?? \SWOOLE_SOCK_TCP,
            'mode'       => $this->config['mode'] ?? \SWOOLE_BASE,
            'ssl'        => $this->config['ssl'] ?? false,
            'reuse_port' => $this->config['reuse_port'] ?? true,
        ];
    }

    /**
     * 绑定服务器事件.
     */
    protected function __bindEvents(): void
    {
        Event::one('IMI.MAIN_SERVER.WORKER.START.APP', function (WorkerStartEventParam $e) {
            // 内置事件监听
            $this->on('request', [new BeforeRequest($this), 'handle'], ImiPriority::IMI_MAX);
            if ($this->http2)
            {
                $this->on('close', [new Http2BeforeClose(), 'handle'], ImiPriority::IMI_MAX);
                $this->on('close', [new Http2AfterClose(), 'handle'], ImiPriority::IMI_MIN);
            }
        });

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
                    if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                    {
                        App::getBean('ErrorLog')->onException($th);
                    }
                }
            });
        }
        else
        {
            $this->swoolePort->on('request', function () {
            });
        }

        if ($event = ($events['close'] ?? false) || $this->http2)
        {
            // @phpstan-ignore-next-line
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (HttpServer $server, int $fd, int $reactorId) {
                try
                {
                    $this->trigger('close', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                    ], $this, CloseEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('close', function () {
            });
        }
    }

    /**
     * 是否为 https 服务
     */
    public function isSSL(): bool
    {
        return $this->https;
    }

    /**
     * 是否为 http2 服务
     */
    public function isHttp2(): bool
    {
        return $this->http2;
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return $this->isHttp2();
    }
}
