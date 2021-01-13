<?php

declare(strict_types=1);

namespace Imi\Swoole\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\StatusCode;

class SwooleResponse extends Response
{
    /**
     * swoole响应对象
     *
     * @var \Swoole\Http\Response
     */
    protected \Swoole\Http\Response $swooleResponse;

    /**
     * 对应的服务器.
     *
     * @var \Imi\Swoole\Server\Base
     */
    protected \Imi\Swoole\Server\Base $serverInstance;

    public function __construct(\Imi\Swoole\Server\Base $server, \Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
        $this->serverInstance = $server;
        parent::__construct();
    }

    /**
     * 发送头部信息，没有特别需求，无需手动调用.
     *
     * @return static
     */
    private function sendHeaders()
    {
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

        return $this;
    }

    /**
     * 设置服务器端重定向
     * 默认状态码为302.
     *
     * @param string $url
     * @param int    $status
     *
     * @return static
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): self
    {
        return $this->setStatus($status)->setHeader('location', $url);
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
        $this->swooleResponse->end($this->getBody());

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
        $this->swooleResponse->sendfile($filename, $offset, $length);

        return $this;
    }

    /**
     * 获取swoole响应对象
     *
     * @return \Swoole\Http\Response
     */
    public function getSwooleResponse(): \Swoole\Http\Response
    {
        return $this->swooleResponse;
    }

    /**
     * 获取对应的服务器.
     *
     * @return \Imi\Swoole\Server\Base
     */
    public function getServerInstance(): \Imi\Swoole\Server\Base
    {
        return $this->serverInstance;
    }
}
