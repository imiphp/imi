<?php

declare(strict_types=1);

namespace Imi\Swoole\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\StatusCode;

class SwooleResponse extends Response
{
    /**
     * swoole响应对象
     */
    protected ?\Swoole\Http\Response $swooleResponse = null;

    /**
     * 对应的服务器.
     */
    protected ?ISwooleServer $serverInstance = null;

    public function __construct(ISwooleServer $server, \Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
        $this->serverInstance = $server;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function isHeaderWritable(): bool
    {
        return $this->swooleResponse->isWritable();
    }

    /**
     * {@inheritDoc}
     */
    public function isBodyWritable(): bool
    {
        return $this->swooleResponse->isWritable();
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
        $swooleResponse = $this->swooleResponse;
        // cookie
        if ($this->cookies)
        {
            foreach ($this->cookies as $cookie)
            {
                $swooleResponse->cookie($cookie['key'], $cookie['value'], $cookie['expire'] ?? 0, $cookie['path'] ?? '/', $cookie['domain'] ?? '', $cookie['secure'] ?? false, $cookie['httponly'] ?? false);
            }
        }
        // header
        if ($this->headers)
        {
            foreach ($this->headers as $name => $headers)
            {
                $swooleResponse->header($name, $this->getHeaderLine($name));
            }
        }
        // trailer
        if ($this->trailers)
        {
            foreach ($this->trailers as $name => $value)
            {
                $swooleResponse->trailer($name, $value);
            }
        }
        // status
        if (StatusCode::OK !== $this->statusCode)
        {
            $swooleResponse->status($this->statusCode);
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
            $this->swooleResponse->end($this->getBody());
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
            $this->swooleResponse->sendfile($filename, $offset, $length);
        }

        return $this;
    }

    /**
     * 获取swoole响应对象
     */
    public function getSwooleResponse(): \Swoole\Http\Response
    {
        return $this->swooleResponse;
    }

    /**
     * 获取对应的服务器.
     */
    public function getServerInstance(): ISwooleServer
    {
        return $this->serverInstance;
    }
}
