<?php

namespace Imi\Test\WebSocketServer\MainServer\Controller\Http;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\WebSocket\WSConfig;
use Imi\Server\View\Annotation\View;

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
     *
     * @return void
     */
    public function index()
    {
        // 握手处理，什么都不做，框架会帮你做好
    }

    /**
     * @Action
     * @Route("/test2")
     * @WSConfig(parserClass=\Imi\Server\DataParser\JsonObjectParser::class)
     *
     * @return void
     */
    public function test2()
    {
    }

    /**
     * @Action
     * @Route("/http")
     *
     * @return void
     */
    public function http()
    {
        return $this->response->write('http');
    }
}
