<?php

declare(strict_types=1);

namespace Imi\Server\Grpc;

use Imi\Server\Grpc\Error\GrpcErrorHandler;

/**
 * gRPC 服务器类.
 */
class Server extends \Imi\Swoole\Server\Http\Server
{
    /**
     * 创建 swoole 服务器对象
     */
    protected function createServer(): void
    {
        $this->initGrpcServer();
        $this->config['configs']['open_http2_protocol'] = true;
        parent::createServer();
    }

    /**
     * 从主服务器监听端口，作为子服务器.
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
