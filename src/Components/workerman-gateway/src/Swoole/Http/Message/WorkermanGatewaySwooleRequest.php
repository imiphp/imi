<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Http\Message;

use Imi\RequestContext;
use Imi\Server\Http\Message\Request;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Util\Socket\IPEndPoint;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

if (\Imi\Util\Imi::checkAppType('swoole'))
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
         * {@inheritDoc}
         */
        protected function initProtocolVersion(): void
        {
            [, $this->protocolVersion] = explode('/', $this->data['server']['SERVER_PROTOCOL']);
        }

        /**
         * {@inheritDoc}
         */
        protected function initHeaders(): void
        {
            $headers = [];
            foreach ($this->data['server'] as $name => $value)
            {
                if ('HTTP_' === substr($name, 0, 5))
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
            $this->body = new MemoryStream('');
        }

        /**
         * {@inheritDoc}
         */
        protected function initUri(): void
        {
            $data = $this->data;
            $this->uri = new Uri('ws://' . $data['server']['HTTP_HOST'] . $data['server']['REQUEST_URI']);
        }

        /**
         * {@inheritDoc}
         */
        protected function initMethod(): void
        {
            $this->method = $this->data['server']['REQUEST_METHOD'];
        }

        /**
         * {@inheritDoc}
         */
        protected function initServer(): void
        {
            $this->server = $this->data['server'];
        }

        /**
         * {@inheritDoc}
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
         * {@inheritDoc}
         */
        public function getClientAddress(): IPEndPoint
        {
            return RequestContext::getServer()->getClientAddress($this->clientId);
        }
    }
}
