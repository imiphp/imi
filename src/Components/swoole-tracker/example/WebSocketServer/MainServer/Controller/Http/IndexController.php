<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\WebSocketServer\MainServer\Controller\Http;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;
use Imi\Server\WebSocket\Route\Annotation\WSConfig;

/**
 * 测试.
 */
#[Controller]
#[View(renderType: 'html')]
class IndexController extends HttpController
{
    /**
     * 连接地址：ws://127.0.0.1:8083/.
     */
    #[Action]
    #[Route(url: '/')]
    #[WSConfig(parserClass: \Imi\Server\DataParser\JsonObjectParser::class)]
    public function index(): void
    {
        // 握手处理，什么都不做，框架会帮你做好
    }
}
