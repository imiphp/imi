<?php

declare(strict_types=1);

namespace ApiDocApp\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @OA\Info(title="My First API", version="0.1")
 */
#[Controller(prefix: '/')]
class IndexController extends HttpController
{
    #[Action]
    #[Route(url: '/')]
    public function index(): void
    {
    }

    /**
     * @param string $username 用户名
     * @param int    $password 密码
     */
    #[Action]
    #[Route(url: 'login', method: 'POST')]
    public function login(string $username, int $password): void
    {
    }

    #[Action]
    #[Route(method: ['GET', 'POST'])]
    public function multiMethod1(int $id, int $type, array $tags): void
    {
    }

    /**
     * @param int[] $tags 标签
     */
    #[Action]
    #[Route(method: ['PUT', 'POST'])]
    public function multiMethod2(int $id, int $type, array $tags): void
    {
    }

    /**
     * @OA\Get(
     *     path="/register",
     *
     *     @OA\Response(response="200", description="An example resource")
     * )
     *
     * @param string $username 用户名
     * @param int    $password 密码
     * @param string $birthday 生日
     */
    #[Action]
    #[Route(url: 'register')]
    public function register(string $username, int $password, string $birthday): void
    {
    }

    #[Action]
    public function get(int $id): void
    {
    }
}
