<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Http\Message;

use Imi\RequestContext;
use Imi\Server\Http\Message\Request;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Util\Socket\IPEndPoint;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

if (\extension_loaded('swoole'))
{
    class WorkermanGatewaySwooleRequest extends Request
    {
        /**
         * 对应的服务器.
         */
        protected ISwooleServer $serverInstance;

        protected string $clientId;

        protected array $data = [];

        public function __construct(ISwooleServer $server, string $clientId, array $data)
        {
            $this->serverInstance = $server;
            $this->clientId = $clientId;
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
         * 获取对应的服务器.
         */
        public function getServerInstance(): ISwooleServer
        {
            return $this->serverInstance;
        }

        public function getClientId(): string
        {
            return $this->clientId;
        }

        public function getData(): array
        {
            return $this->data;
        }

        /**
         * 获取客户端地址
         */
        public function getClientAddress(): IPEndPoint
        {
            return RequestContext::getServer()->getClientAddress($this->clientId);
        }
    }
}
