<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;
use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\StreamMode;

class RoadRunnerResponse extends Response
{
    /**
     * 被更改过的 Cookie 名称数组.
     */
    protected array $changedCookieNames = [];

    protected \Spiral\RoadRunner\Http\PSR7Worker $worker;

    public function __construct(\Spiral\RoadRunner\Http\PSR7Worker $worker)
    {
        parent::__construct();
        $this->worker = $worker;
    }

    /**
     * 响应头是否可写.
     */
    public function isHeaderWritable(): bool
    {
        return !connection_aborted() && !headers_sent();
    }

    /**
     * 响应主体是否可写.
     */
    public function isBodyWritable(): bool
    {
        return !connection_aborted();
    }

    /**
     * 设置cookie.
     *
     * @return static
     */
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        $self = parent::withCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        $self->changedCookieNames[$key] = true;

        return $self;
    }

    /**
     * 设置cookie.
     *
     * @return static
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        parent::setCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        $this->changedCookieNames[$key] = true;

        return $this;
    }

    /**
     * 发送所有响应数据.
     *
     * @return static
     */
    public function send(): self
    {
        if ($this->changedCookieNames)
        {
            $response = $this;
            foreach ($this->changedCookieNames as $name => $_)
            {
                $response->addHeader(ResponseHeader::SET_COOKIE, $this->cookieArrayToHeader($this->getCookie($name)));
            }
            $this->worker->respond($response);
        }
        else
        {
            $this->worker->respond($this);
        }

        return $this;
    }

    public function cookieArrayToHeader(array $cookie): string
    {
        $header = rawurlencode($cookie['key']) . '=' . rawurlencode($cookie['value']);
        if ($cookie['expire'] > 0)
        {
            $header .= '; Expires=' . gmdate(\DateTime::COOKIE, $cookie['expire']);
        }
        if ('' !== $cookie['path'])
        {
            $header .= '; Path=' . $cookie['path'];
        }
        if ('' !== $cookie['domain'])
        {
            $header .= '; Domain=' . $cookie['domain'];
        }
        if ($cookie['secure'])
        {
            $header .= '; Secure';
        }
        if ($cookie['httponly'])
        {
            $header .= '; HttpOnly';
        }

        return $header;
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

        $this->setBody(new FileStream($filename, StreamMode::READONLY));

        return $this;
    }
}
