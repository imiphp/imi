<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\StatusCode;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class WorkermanResponse extends Response
{
    /**
     * Workerman 的 http 响应对象
     */
    protected ?\Workerman\Protocols\Http\Response $workermanResponse = null;

    /**
     * Workerman 的 Worker 对象
     */
    protected ?Worker $worker = null;

    /**
     * Workerman 连接对象
     */
    protected ?TcpConnection $connection = null;

    /**
     * 响应头是否可写.
     */
    protected bool $isHeaderWritable = true;

    /**
     * 响应主体是否可写.
     */
    protected bool $isBodyWritable = true;

    public function __construct(Worker $worker, TcpConnection $connection, ?\Workerman\Protocols\Http\Response $response = null)
    {
        $this->workermanResponse = $response;
        $this->worker = $worker;
        $this->connection = $connection;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function isHeaderWritable(): bool
    {
        return $this->workermanResponse && $this->isHeaderWritable;
    }

    /**
     * {@inheritDoc}
     */
    public function isBodyWritable(): bool
    {
        return $this->workermanResponse && $this->isBodyWritable;
    }

    /**
     * 发送头部信息，没有特别需求，无需手动调用.
     */
    private function sendHeaders(): void
    {
        if (!$this->isHeaderWritable())
        {
            return;
        }
        $this->isHeaderWritable = false;
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
     * {@inheritDoc}
     */
    public function send(): self
    {
        $this->sendHeaders();
        if ($this->isBodyWritable())
        {
            $this->isBodyWritable = false;
            $response = $this->workermanResponse;
            $response->withBody((string) $this->getBody());
            $this->connection->send($response);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function sendFile(string $filename, ?string $contentType = null, ?string $outputFileName = null, int $offset = 0, int $length = 0): self
    {
        if (null === $outputFileName)
        {
            $outputFileName = basename($filename);
        }
        $this->setHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . rawurlencode($outputFileName));

        if (null === $contentType)
        {
            $outputFileNameExt = pathinfo($outputFileName, \PATHINFO_EXTENSION);
            $contentType = MediaType::getContentType($outputFileNameExt);
            if (MediaType::APPLICATION_OCTET_STREAM === $contentType)
            {
                $fileNameExt = pathinfo($filename, \PATHINFO_EXTENSION);
                if ($fileNameExt !== $outputFileNameExt)
                {
                    $contentType = MediaType::getContentType($fileNameExt);
                }
            }
        }
        $this->setHeader('Content-Type', $contentType);

        $this->sendHeaders();
        if ($this->isBodyWritable())
        {
            $this->isBodyWritable = false;
            $response = $this->workermanResponse;
            $response->withFile($filename, $offset, $length);
            $this->connection->send($response);
        }

        return $this;
    }

    /**
     * Get workerman 的 http 响应对象
     */
    public function getWorkermanResponse(): ?\Workerman\Protocols\Http\Response
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
