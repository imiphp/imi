<?php

declare(strict_types=1);

namespace Imi\Fpm\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Util\Socket\IPEndPoint;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class FpmRequest extends Request
{
    /**
     * 客户端地址
     */
    protected IPEndPoint $clientAddress;

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): IPEndPoint
    {
        if (!isset($this->clientAddress))
        {
            return $this->clientAddress = new IPEndPoint($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT']);
        }

        return $this->clientAddress;
    }

    /**
     * {@inheritDoc}
     */
    protected function initProtocolVersion(): void
    {
        $this->protocolVersion = substr($_SERVER['SERVER_PROTOCOL'], 5);
    }

    /**
     * {@inheritDoc}
     */
    protected function initHeaders(): void
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (str_starts_with($name, 'HTTP_'))
            {
                $headers[str_replace('_', '-', substr($name, 5))] = $value;
            }
        }
        $this->mergeHeaders($headers);
    }

    /**
     * {@inheritDoc}
     */
    protected function initBody(): void
    {
        $this->body = new MemoryStream(file_get_contents('php://input'));
    }

    /**
     * {@inheritDoc}
     */
    protected function initUri(): void
    {
        $https = $_SERVER['HTTPS'] ?? null;
        if ('on' === $https || '1' === $https || 'https' === ($_SERVER['REQUEST_SCHEME'] ?? null))
        {
            $url = 'https://';
        }
        else
        {
            $url = 'http://';
        }

        if (isset($_SERVER['PATH_INFO']))
        {
            $path = $_SERVER['PATH_INFO'];
            if (isset($_SERVER['QUERY_STRING']))
            {
                $path .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        else
        {
            $path = $_SERVER['REQUEST_URI'];
        }
        $this->uri = new Uri($url . $_SERVER['HTTP_HOST'] . $path);
    }

    /**
     * {@inheritDoc}
     */
    protected function initMethod(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * {@inheritDoc}
     */
    protected function initServer(): void
    {
        $this->server = $_SERVER;
    }

    /**
     * {@inheritDoc}
     */
    protected function initRequestParams(): void
    {
        $this->get = $_GET;
        if ($_POST)
        {
            $this->post = $_POST;
        }
        $this->cookies = $_COOKIE;
        $this->request = $_REQUEST;
    }

    /**
     * {@inheritDoc}
     */
    protected function initUploadedFiles(): void
    {
        $this->setUploadedFiles($_FILES);
    }
}
