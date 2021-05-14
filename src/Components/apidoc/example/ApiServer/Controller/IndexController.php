<?php

namespace ApiDocApp\ApiServer\Controller;

use Imi\Controller\SingletonHttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Route;

/**
 * @OA\Info(title="My First API", version="0.1")
 * @Controller("/")
 */
class IndexController extends SingletonHttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return void
     */
    public function index()
    {
    }

    /**
     * @Action
     * @Route(url="login", method="POST")
     *
     * @param string $username 用户名
     * @param int    $password 密码
     *
     * @return void
     */
    public function login(string $username, int $password)
    {
    }

    /**
     * @Action
     * @Route(method={"GET", "POST"})
     *
     * @return void
     */
    public function multiMethod1(int $id)
    {
    }

    /**
     * @Action
     * @Route(method={"PUT", "POST"})
     *
     * @return void
     */
    public function multiMethod2(int $id)
    {
    }

    /**
     * @Action
     * @Route("register")
     * @OA\Get(
     *     path="/register",
     *     @OA\Response(response="200", description="An example resource")
     * )
     *
     * @param string $username 用户名
     * @param int    $password 密码
     * @param string $birthday 生日
     *
     * @return void
     */
    public function register(string $username, int $password, string $birthday)
    {
    }

    /**
     * @Action
     *
     * @param int $id
     *
     * @return void
     */
    public function get(int $id)
    {
    }
}
