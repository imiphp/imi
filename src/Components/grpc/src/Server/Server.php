<?php

declare(strict_types=1);

namespace Imi\Grpc\Server;

use Imi\Bean\Annotation\Bean;
use Imi\Grpc\Server\Error\GrpcErrorHandler;

/**
 * gRPC 服务器类.
 */
#[Bean(name: 'GrpcServer', env: 'swoole')]
class Server extends \Imi\Swoole\Server\Http\Server
{
    /**
     * {@inheritDoc}
     */
    protected function createServer(): void
    {
        $this->initGrpcServer();
        $this->config['configs']['open_http2_protocol'] = true;
        parent::createServer();
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        $this->initGrpcServer();
        $this->config['configs']['open_http2_protocol'] = true;
        parent::createSubServer();
    }

    /**
     * 初始化 gRPC 服务器.
     */
    private function initGrpcServer(): void
    {
        $this->container->bind('HttpErrorHandler', GrpcErrorHandler::class);
    }
}
