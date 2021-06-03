<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Request;
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
     * 初始化协议版本.
     */
    protected function initProtocolVersion(): void
    {
        $this->protocolVersion = $this->workermanRequest->protocolVersion();
    }

    /**
     * 初始化 headers.
     */
    protected function initHeaders(): void
    {
        // @phpstan-ignore-next-line
        $this->mergeHeaders($this->workermanRequest->header());
    }

    /**
     * 初始化 body.
     */
    protected function initBody(): void
    {
        $this->body = new MemoryStream($this->workermanRequest->rawBody());
    }

    /**
     * 初始化 uri.
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
     * 初始化 method.
     */
    protected function initMethod(): void
    {
        $this->method = $this->workermanRequest->method();
    }

    /**
     * 初始化 server.
     */
    protected function initServer(): void
    {
        $this->server = [];
    }

    /**
     * 初始化请求参数.
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
     * 初始化上传文件.
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
}
