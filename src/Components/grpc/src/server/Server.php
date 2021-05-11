<?php

namespace Imi\Server\Grpc;

use Imi\Server\Grpc\Error\GrpcErrorHandler;

/**
 * gRPC 服务器类.
 */
class Server extends \Imi\Server\Http\Server
{
    /**
     * 创建 swoole 服务器对象
     *
     * @return void
     */
    protected function createServer()
    {
        $this->initGrpcServer();
        $this->config['configs']['open_http2_protocol'] = true;
        parent::createServer();
    }

    /**
     * 从主服务器监听端口，作为子服务器.
     *
     * @return void
     */
    protected function createSubServer()
    {
        $this->initGrpcServer();
        $this->config['configs']['open_http2_protocol'] = true;
        parent::createSubServer();
    }

    /**
     * 初始化 gRPC 服务器.
     *
     * @return void
     */
    private function initGrpcServer()
    {
        $this->container->bind('HttpErrorHandler', GrpcErrorHandler::class);
    }
}
