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
    protected \Swoole\Http\Response $swooleResponse;

    /**
     * 对应的服务器.
     */
    protected ISwooleServer $serverInstance;

    public function __construct(ISwooleServer $server, \Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
        $this->serverInstance = $server;
        parent::__construct();
    }

    /**
     * 响应头是否可写.
     */
    public function isHeaderWritable(): bool
    {
        return $this->swooleResponse->isWritable();
    }

    /**
     * 响应主体是否可写.
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

        return;
    }

    /**
     * 发送所有响应数据.
     *
     * @return static
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
     * 发送文件，一般用于文件下载.
     *
     * @param string      $filename       要发送的文件名称，文件不存在或没有访问权限sendfile会失败
     * @param string|null $contentType    Content-Type 响应头，不填则自动识别
     * @param string|null $outputFileName 下载文件名，不填则自动识别，如：123.zip
     * @param int         $offset         上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
     * @param int         $length         发送数据的尺寸，默认为整个文件的尺寸
     *
     * @return static
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
