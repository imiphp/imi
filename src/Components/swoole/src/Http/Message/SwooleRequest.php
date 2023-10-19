<?php

declare(strict_types=1);

namespace Imi\Swoole\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Util\Socket\IPEndPoint;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class SwooleRequest extends Request
{
    /**
     * @param \Imi\Swoole\Server\Http\Server|\Imi\Swoole\Server\WebSocket\Server $serverInstance
     */
    public function __construct(
        /**
         * 对应的服务器.
         */
        protected ?ISwooleServer $serverInstance,
        /**
         * swoole的http请求对象
         */
        protected ?\Swoole\Http\Request $swooleRequest
    ) {
    }

    /**
     * {@inheritDoc}
     */
    protected function initProtocolVersion(): void
    {
        [, $this->protocolVersion] = explode('/', (string) $this->swooleRequest->server['server_protocol'], 2);
    }

    /**
     * {@inheritDoc}
     */
    protected function initHeaders(): void
    {
        $this->mergeHeaders($this->swooleRequest->header);
    }

    /**
     * {@inheritDoc}
     */
    protected function initBody(): void
    {
        $this->body = new MemoryStream($this->swooleRequest->rawContent() ?: '');
    }

    /**
     * {@inheritDoc}
     */
    protected function initUri(): void
    {
        $serverInstance = $this->serverInstance;
        if ($serverInstance instanceof \Imi\Swoole\Server\Http\Server)
        {
            $scheme = $serverInstance->isSSL() ? 'https' : 'http';
        }
        elseif ($serverInstance instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $scheme = $serverInstance->isSSL() ? 'wss' : 'ws';
        }
        else
        {
            $scheme = 'http';
        }
        $swooleRequest = $this->swooleRequest;
        $get = $swooleRequest->get;

        $host = $swooleRequest->header['host'] ?? null;
        if ($host)
        {
            $port = null;
        }
        else
        {
            $host = '127.0.0.1';
            $port = $swooleRequest->server['server_port'];
        }

        $this->uri = Uri::makeUri($host, $swooleRequest->server['path_info'], null === $get ? '' : (http_build_query($get, '', '&')), $port, $scheme);
    }

    /**
     * {@inheritDoc}
     */
    protected function initMethod(): void
    {
        $this->method = $this->swooleRequest->server['request_method'];
    }

    /**
     * {@inheritDoc}
     */
    protected function initServer(): void
    {
        $this->server = $this->swooleRequest->server;
    }

    /**
     * {@inheritDoc}
     */
    protected function initRequestParams(): void
    {
        $request = $this->swooleRequest;
        $this->get = $request->get ?? [];
        $this->post = $request->post ?? [];
        $this->cookies = $request->cookie ?? [];
        $this->request = null;
    }

    /**
     * {@inheritDoc}
     */
    protected function initUploadedFiles(): void
    {
        $this->setUploadedFiles($this->swooleRequest->files ?? []);
    }

    /**
     * Get swoole的http请求对象
     */
    public function getSwooleRequest(): \Swoole\Http\Request
    {
        return $this->swooleRequest;
    }

    /**
     * 获取对应的服务器.
     */
    public function getServerInstance(): Base
    {
        return $this->serverInstance;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): IPEndPoint
    {
        return $this->serverInstance->getClientAddress($this->swooleRequest->fd);
    }
}
