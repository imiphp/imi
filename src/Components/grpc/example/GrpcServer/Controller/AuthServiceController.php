<?php

declare(strict_types=1);

namespace GrpcApp\GrpcServer\Controller;

use Grpc\AuthServiceInterface;
use Grpc\LoginRequest;
use Grpc\LoginResponse;
use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

/**
 * @Controller("/grpc.AuthService/")
 */
class AuthServiceController extends HttpController implements AuthServiceInterface
{
    /**
     * Method <code>login</code>.
     *
     * @Action
     *
     * @return \Grpc\LoginResponse
     */
    public function login(LoginRequest $request)
    {
        $response = new LoginResponse();
        $success = '12345678901' === $request->getPhone() && '123456' === $request->getPassword();
        $response->setSuccess($success);
        $response->setError($success ? '' : '登录失败');

        return $response;
    }
}
