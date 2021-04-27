<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Controller\Http;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;
use Imi\Server\WebSocket\Route\Annotation\WSConfig;

/**
 * 测试.
 *
 * @Controller
 * @View(renderType="html")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route("/")
     * @WSConfig(parserClass=\Imi\Server\DataParser\JsonObjectParser::class)
     */
    public function index(): void
    {
        // 握手处理，什么都不做，框架会帮你做好
    }

    /**
     * @Action
     * @Route("/test2")
     * @WSConfig(parserClass=\Imi\Server\DataParser\JsonObjectParser::class)
     */
    public function test2(): void
    {
    }

    /**
     * @Action
     * @Route("/http")
     *
     * @return mixed
     */
    public function http()
    {
        $response = $this->response;
        $response->getBody()->write('http');

        return $response;
    }
}
