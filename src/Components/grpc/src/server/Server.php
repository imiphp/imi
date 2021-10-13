<?php

declare(strict_types=1);

namespace Imi\Server\Grpc;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Grpc\Error\GrpcErrorHandler;

/**
 * @Bean(name="GrpcServer", env="swoole")
 * gRPC 服务器类.
 */
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
