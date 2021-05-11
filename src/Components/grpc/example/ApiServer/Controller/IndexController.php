<?php

namespace GrpcApp\ApiServer\Controller;

use Grpc\LoginRequest;
use Imi\Controller\HttpController;
use Imi\Grpc\Client\Annotation\GrpcService;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Route;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @GrpcService(serviceName="grpc.AuthService", interface=\Grpc\AuthServiceInterface::class)
     *
     * @var \Grpc\AuthServiceInterface
     */
    protected $authService;

    /**
     * @Action
     * @Route("/")
     *
     * @return void
     */
    public function index()
    {
        return $this->response;
    }

    /**
     * 测试登录.
     *
     * @Action
     *
     * @param string $phone
     * @param string $password
     *
     * @return void
     */
    public function testLogin($phone, $password)
    {
        $request = new LoginRequest();
        $request->setPhone($phone);
        $request->setPassword($password);
        $response = $this->authService->login($request, \Grpc\LoginResponse::class);
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
