<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Swoole\Http\Message\SwooleRequest;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleHttpServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Server\Http\Listener\BeforeRequest;
use Imi\Swoole\Server\Http\Listener\Http2AfterClose;
use Imi\Swoole\Server\Http\Listener\Http2BeforeClose;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Util\Bit;
use Imi\Util\ImiPriority;
use Imi\Worker;
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
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * {@inheritDoc}
     */
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\Http\Server($config['host'], (int) $config['port'], (int) $config['mode'], (int) $config['sockType']);
        $this->https = \defined('SWOOLE_SSL') && Bit::has((int) $config['sockType'], \SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        parent::createSubServer();
        $thisConfig = &$this->config;
        $thisConfig['configs']['open_http_protocol'] ??= true;
        $this->https = \defined('SWOOLE_SSL') && isset($thisConfig['sockType']) && Bit::has((int) $thisConfig['sockType'], \SWOOLE_SSL);
        $this->http2 = $thisConfig['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * {@inheritDoc}
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'       => $host = $this->config['host'] ?? '0.0.0.0',
            'port'       => (int) ($this->config['port'] ?? 80),
            'sockType'   => (int) ($this->config['sockType'] ?? \Imi\Swoole\Util\Swoole::getTcpSockTypeByHost($host)),
            'mode'       => (int) ($this->config['mode'] ?? \SWOOLE_BASE),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function __bindEvents(): void
    {
        Event::one('IMI.MAIN_SERVER.WORKER.START.APP', function (WorkerStartEventParam $e): void {
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
            $this->swoolePort->on('request', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse): void {
                try
                {
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
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
                    // @phpstan-ignore-next-line
                    if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                    {
                        Log::error($th);
                    }
                }
            });
        }
        else
        {
            $this->swoolePort->on('request', static function (): void {
            });
        }

        if ($event = ($events['close'] ?? false) || $this->http2)
        {
            // @phpstan-ignore-next-line
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId): void {
                try
                {
                    $this->trigger('close', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                    ], $this, CloseEventParam::class);
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });
        }
        else
        {
            $this->swoolePort->on('close', static function (): void {
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSSL(): bool
    {
        return $this->https;
    }

    /**
     * {@inheritDoc}
     */
    public function isHttp2(): bool
    {
        return $this->http2;
    }

    /**
     * {@inheritDoc}
     */
    public function isLongConnection(): bool
    {
        return $this->isHttp2();
    }
}
