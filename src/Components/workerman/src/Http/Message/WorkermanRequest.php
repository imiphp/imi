<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Util\Socket\IPEndPoint;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class WorkermanRequest extends Request
{
    /**
     * Workerman 的 http 请求对象
     */
    protected \Workerman\Protocols\Http\Request $workermanRequest;

    /**
     * Workerman 的 Worker 对象
     */
    protected Worker $worker;

    /**
     * 协议.
     */
    protected string $scheme;

    protected TcpConnection $connection;

    public function __construct(Worker $worker, TcpConnection $connection, \Workerman\Protocols\Http\Request $request, string $scheme = 'http')
    {
        $this->workermanRequest = $request;
        $this->worker = $worker;
        $this->connection = $connection;
        $this->scheme = $scheme;
    }

    /**
     * {@inheritDoc}
     */
    protected function initProtocolVersion(): void
    {
        $this->protocolVersion = $this->workermanRequest->protocolVersion();
    }

    /**
     * {@inheritDoc}
     */
    protected function initHeaders(): void
    {
        // @phpstan-ignore-next-line
        $this->mergeHeaders($this->workermanRequest->header());
    }

    /**
     * {@inheritDoc}
     */
    protected function initBody(): void
    {
        $this->body = new MemoryStream($this->workermanRequest->rawBody());
    }

    /**
     * {@inheritDoc}
     */
    protected function initUri(): void
    {
        $worker = $this->worker;
        $isSSL = 'ssl' === $worker->transport;
        $scheme = $this->scheme;
        if ($isSSL)
        {
            $scheme .= 's';
        }
        $workerRequest = $this->workermanRequest;

        $get = $workerRequest->get();

        $this->uri = Uri::makeUri($workerRequest->host(), $workerRequest->path(), null === $get ? '' : (http_build_query($get, '', '&')), null, $scheme);
    }

    /**
     * {@inheritDoc}
     */
    protected function initMethod(): void
    {
        $this->method = $this->workermanRequest->method();
    }

    /**
     * {@inheritDoc}
     */
    protected function initServer(): void
    {
        $this->server = [];
    }

    /**
     * {@inheritDoc}
     */
    protected function initRequestParams(): void
    {
        $request = $this->workermanRequest;
        $this->get = $request->get();
        $this->post = $request->post();
        $this->cookies = $request->cookie() ?? [];
        $this->request = null;
    }

    /**
     * {@inheritDoc}
     */
    protected function initUploadedFiles(): void
    {
        $this->setUploadedFiles($this->workermanRequest->file());
    }

    /**
     * Get workerman 的 http 请求对象
     */
    public function getWorkermanRequest(): \Workerman\Protocols\Http\Request
    {
        return $this->workermanRequest;
    }

    /**
     * Get workerman 的 Worker 对象
     */
    public function getWorker(): Worker
    {
        return $this->worker;
    }

    /**
     * Get workerman 连接对象
     */
    public function getConnection(): TcpConnection
    {
        return $this->connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): IPEndPoint
    {
        $connection = $this->workermanRequest->connection;

        return new IPEndPoint($connection->getRemoteIp(), $connection->getRemotePort());
    }
}
