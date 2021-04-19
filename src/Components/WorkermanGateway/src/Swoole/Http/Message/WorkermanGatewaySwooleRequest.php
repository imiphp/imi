<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class WorkermanGatewaySwooleRequest extends Request
{
    /**
     * 对应的服务器.
     */
    protected ISwooleServer $serverInstance;

    protected array $data = [];

    public function __construct(ISwooleServer $server, array $data)
    {
        $this->serverInstance = $server;
        $this->data = $data;
    }

    /**
     * 初始化协议版本.
     */
    protected function initProtocolVersion(): void
    {
        [, $this->protocolVersion] = explode('/', $this->data['server']['SERVER_PROTOCOL']);
    }

    /**
     * 初始化 headers.
     */
    protected function initHeaders(): void
    {
        $headers = [];
        foreach ($this->data['server'] as $name => $value)
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
     */
    protected function initBody(): void
    {
        $this->body = new MemoryStream('');
    }

    /**
     * 初始化 uri.
     */
    protected function initUri(): void
    {
        $data = $this->data;
        $this->uri = new Uri('ws://' . $data['server']['HTTP_HOST'] . $data['server']['REQUEST_URI']);
    }

    /**
     * 初始化 method.
     */
    protected function initMethod(): void
    {
        $this->method = $this->data['server']['REQUEST_METHOD'];
    }

    /**
     * 初始化 server.
     */
    protected function initServer(): void
    {
        $this->server = $this->data['server'];
    }

    /**
     * 初始化请求参数.
     */
    protected function initRequestParams(): void
    {
        $data = $this->data;
        $this->get = $data['get'];
        $this->post = [];
        $this->cookies = $data['cookie'];
        $this->request = null;
    }

    /**
     * Get swoole的http请求对象
     */
    public function getSwooleRequest(): \Swoole\Http\Request
    {
        return $this->swooleRequest;
    }

    /**
     * Get the value of data.
     */
    public function getData(): array
    {
        return $this->data;
    }
}
