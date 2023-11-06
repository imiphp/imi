<?php

declare(strict_types=1);

namespace GrpcApp\ApiServer\Controller;

use Grpc\LoginRequest;
use Imi\Controller\HttpController;
use Imi\Grpc\Client\Annotation\GrpcService;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

#[Controller(prefix: '/')]
class IndexController extends HttpController
{
    /**
     * @var \Grpc\AuthServiceInterface
     */
    #[GrpcService(serviceName: 'grpc.AuthService', interface: 'Grpc\\AuthServiceInterface')]
    protected $authService;

    #[Action]
    #[Route(url: '/')]
    public function index(): mixed
    {
        return $this->response;
    }

    /**
     * 测试登录.
     */
    #[Action]
    public function testLogin(string $phone, string $password): array
    {
        $request = new LoginRequest();
        $request->setPhone($phone);
        $request->setPassword($password);
        // @phpstan-ignore-next-line
        $response = $this->authService->login($request, \Grpc\LoginResponse::class);
        // @phpstan-ignore-next-line
        if (!$response)
        {
            throw new \RuntimeException('GG');
        }

        return [
            'success'   => $response->getSuccess(),
            'error'     => $response->getError(),
        ];
    }
}
