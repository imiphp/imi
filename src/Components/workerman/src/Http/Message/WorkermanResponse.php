<?php

declare(strict_types=1);

namespace Imi\Workerman\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;
use Imi\Util\Http\Consts\StatusCode;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class WorkermanResponse extends Response
{
    /**
     * 响应头是否可写.
     */
    protected bool $isHeaderWritable = true;

    /**
     * 响应主体是否可写.
     */
    protected bool $isBodyWritable = true;

    protected bool $emitterWritting = false;

    public function __construct(
        /**
         * Workerman 的 Worker 对象
         */
        protected ?Worker $worker,
        /**
         * Workerman 连接对象
         */
        protected ?TcpConnection $connection,
        /**
         * Workerman 的 http 响应对象
         */
        protected ?\Workerman\Protocols\Http\Response $workermanResponse = null,
        /**
         * Workerman 的 http 请求对象
         */
        protected ?WorkermanRequest $request = null)
    {
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
                $name = (string) $name;
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
    public function send(): static
    {
        if ($this->responseBodyEmitter)
        {
            if ($this->emitterWritting)
            {
                return $this;
            }
            $this->emitterWritting = true;
            $this->responseBodyEmitter->init($this, new WorkermanEmitHandler($this->connection));
            $this->sendHeaders();
            $this->connection->send($this->workermanResponse->withBody("\r\n"));
            $this->responseBodyEmitter->send();
            $this->connection->close();
        }
        else
        {
            $this->sendHeaders();
            if ($this->isBodyWritable())
            {
                $this->isBodyWritable = false;
                if ($this->shouldKeepAlive())
                {
                    $this->connection->send($this->workermanResponse->withBody((string) $this->getBody()));
                }
                else
                {
                    $this->connection->close($this->workermanResponse->withBody((string) $this->getBody()));
                }
            }
            elseif (!$this->shouldKeepAlive())
            {
                $this->connection->close();
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function sendFile(string $filename, ?string $contentType = null, ?string $outputFileName = null, int $offset = 0, int $length = 0): static
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
            if ($this->shouldKeepAlive())
            {
                $this->connection->send($this->workermanResponse->withFile($filename, $offset, $length));
            }
            else
            {
                $this->connection->close($this->workermanResponse->withFile($filename, $offset, $length));
            }
        }
        elseif (!$this->shouldKeepAlive())
        {
            $this->connection->close();
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

    protected function shouldKeepAlive(): bool
    {
        if (0 === strcasecmp($this->getHeaderLine(ResponseHeader::CONNECTION), 'close'))
        {
            return false;
        }
        if ($request = $this->request)
        {
            if ($request->getProtocolVersion() >= 1.1)
            {
                return 0 !== strcasecmp($request->getHeaderLine('connection'), 'close');
            }
            else
            {
                return 0 === strcasecmp($request->getHeaderLine('connection'), 'keep-alive');
            }
        }

        return true;
    }
}
