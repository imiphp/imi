<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithAmqpRouteServerUtil\MainServer\Controller;

use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;

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
     * @param mixed $data
     */
    public function test($data): array
    {
        return ['data' => $data];
    }
}
