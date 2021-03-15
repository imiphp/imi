<?php

declare(strict_types=1);

namespace Imi\Fpm\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class FpmRequest extends Request
{
    /**
     * 初始化协议版本.
     *
     * @return void
     */
    protected function initProtocolVersion(): void
    {
        $this->protocolVersion = substr($_SERVER['SERVER_PROTOCOL'], 5);
    }

    /**
     * 初始化 headers.
     *
     * @return void
     */
    protected function initHeaders(): void
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if ('HTTP_' === substr($name, 0, 5))
            {
                $headers[strtolower(str_replace('_', '-', substr($name, 5)))] = $value;
            }
        }
        $this->mergeHeaders($headers);
    }

    /**
     * 初始化 body.
     *
     * @return void
     */
    protected function initBody(): void
    {
        $this->body = new MemoryStream(file_get_contents('php://input'));
    }

    /**
     * 初始化 uri.
     *
     * @return void
     */
    protected function initUri(): void
    {
        if ('on' === ($_SERVER['HTTPS'] ?? null))
        {
            $url = 'https://';
        }
        else
        {
            $url = 'http://';
        }

        if ('80' !== $_SERVER['SERVER_PORT'] && '443' !== $_SERVER['SERVER_PORT'])
        {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        }
        else
        {
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }

        $this->uri = new Uri($url);
    }

    /**
     * 初始化 method.
     *
     * @return void
     */
    protected function initMethod(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 初始化 server.
     *
     * @return void
     */
    protected function initServer(): void
    {
        $this->server = $_SERVER;
    }

    /**
     * 初始化请求参数.
     *
     * @return void
     */
    protected function initRequestParams(): void
    {
        $this->get = $_GET;
        if ($_POST)
        {
            $this->post = $_POST;
        }
        else
        {
            $this->post = $this->getParsedBody() ?? [];
        }
        $this->cookies = $_COOKIE;
        $this->request = $_REQUEST;
    }

    /**
     * 初始化上传文件.
     *
     * @return void
     */
    protected function initUploadedFiles(): void
    {
        $this->setUploadedFiles($_FILES);
    }
}
