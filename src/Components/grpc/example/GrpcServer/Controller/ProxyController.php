<?php

declare(strict_types=1);

namespace GrpcApp\GrpcServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Controller\HttpController;
use Imi\Grpc\Proxy\Http\GrpcHttpProxy;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

#[Controller(prefix: '/proxy/')]
class ProxyController extends HttpController
{
    #[Inject(name: 'GrpcHttpProxy')]
    protected GrpcHttpProxy $grpcHttpProxy;

    #[Action]
    #[Route(url: 'grpc/{service}/{method}')]
    public function proxy(string $service, string $method): mixed
    {
        return $this->grpcHttpProxy->proxy('grpc', $this->request, $this->response, $service, $method);
    }
}
