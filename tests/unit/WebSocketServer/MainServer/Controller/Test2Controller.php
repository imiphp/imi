<?php

declare(strict_types=1);

namespace Imi\Test\WebSocketServer\MainServer\Controller;

use Imi\Controller\WebSocketController;
use Imi\Swoole\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Swoole\Server\WebSocket\Route\Annotation\WSController;
use Imi\Swoole\Server\WebSocket\Route\Annotation\WSRoute;

/**
 * 数据收发测试.
 *
 * @WSController(route="/test")
 */
class Test2Controller extends WebSocketController
{
    /**
     * @WSAction
     * @WSRoute({"action"="test"})
     *
     * @param
     *
     * @return void
     */
    public function test($data)
    {
        return ['data' => $data];
    }
}
