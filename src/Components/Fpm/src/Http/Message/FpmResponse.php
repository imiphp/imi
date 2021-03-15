<?php

declare(strict_types=1);

namespace Imi\Fpm\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\StreamMode;

class FpmResponse extends Response
{
    /**
     * 被更改过的 Cookie 名称数组.
     *
     * @var array
     */
    protected array $changedCookieNames = [];

    /**
     * 设置cookie.
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
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
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return static
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        parent::withCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        $this->changedCookieNames[$key] = true;

        return $this;
    }

    /**
     * 发送头部信息，没有特别需求，无需手动调用.
     *
     * @return static
     */
    private function sendHeaders(): self
    {
        // status
        $data = $this->getStatusCode() . ' ' . $this->getReasonPhrase();
        header('HTTP/1.1 ' . $data);
        // 保证FastCGI模式下正常
        header('Status:' . $data);
        // header
        foreach ($this->getHeaders() as $name => $_)
        {
            header($name . ':' . $this->getHeaderLine($name));
        }
        // cookie
        if ($this->changedCookieNames)
        {
            foreach ($this->changedCookieNames as $name => $_)
            {
                $cookie = $this->getCookie($name);
                setcookie($cookie['key'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
            }
        }

        return $this;
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
        echo (string) $this->getBody();

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
        $fs = new FileStream($filename, StreamMode::READONLY);
        if ($offset > 0)
        {
            $fs->seek($offset);
        }
        if ($length > 0)
        {
            echo $fs->read($length);
        }
        else
        {
            echo $fs->getContents();
        }

        return $this;
    }
}
