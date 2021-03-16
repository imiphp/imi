<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\StatusCode;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class WorkermanResponse extends Response
{
    /**
     * Workerman 的 http 响应对象
     */
    protected \Workerman\Protocols\Http\Response $workermanResponse;

    /**
     * Workerman 的 Worker 对象
     */
    protected Worker $worker;

    /**
     * Workerman 连接对象
     */
    protected TcpConnection $connection;

    public function __construct(Worker $worker, TcpConnection $connection, \Workerman\Protocols\Http\Response $response)
    {
        $this->workermanResponse = $response;
        $this->worker = $worker;
        $this->connection = $connection;
        parent::__construct();
    }

    /**
     * 发送头部信息，没有特别需求，无需手动调用.
     */
    private function sendHeaders(): void
    {
        $response = $this->workermanResponse;
        // cookie
        if ($this->cookies)
        {
            $nowTime = time();
            foreach ($this->cookies as $cookie)
            {
                $maxAge = isset($cookie['expire']) ? ($cookie['expire'] - $nowTime) : 0;
                $response->cookie($cookie['key'], $cookie['value'], $maxAge, $cookie['path'] ?? '/', $cookie['domain'] ?? '', $cookie['secure'] ?? false, $cookie['httponly'] ?? false);
            }
        }
        // header
        if ($this->headers)
        {
            foreach ($this->headers as $name => $headers)
            {
                $response->header($name, $this->getHeaderLine($name));
            }
        }
        // status
        if (StatusCode::OK !== $this->statusCode)
        {
            // @phpstan-ignore-next-line
            $response->withStatus($this->statusCode, $this->reasonPhrase);
        }
    }

    /**
     * 发送所有响应数据.
     *
     * @return static
     */
    public function send(): self
    {
        $this->isEnded = true;
        $this->sendHeaders();
        $response = $this->workermanResponse;
        $response->withBody((string) $this->getBody());
        $this->connection->send($response);

        return $this;
    }

    /**
     * 发送文件，一般用于文件下载.
     *
     * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
     * @param int    $offset   上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
     * @param int    $length   发送数据的尺寸，默认为整个文件的尺寸
     *
     * @return static
     */
    public function sendFile(string $filename, int $offset = 0, int $length = 0): self
    {
        $this->isEnded = true;
        $this->sendHeaders();
        $response = $this->workermanResponse;
        $response->withFile($filename, $offset, $length);
        $this->connection->send($response);

        return $this;
    }

    /**
     * Get workerman 的 http 响应对象
     */
    public function getWorkermanResponse(): \Workerman\Protocols\Http\Response
    {
        return $this->workermanResponse;
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
