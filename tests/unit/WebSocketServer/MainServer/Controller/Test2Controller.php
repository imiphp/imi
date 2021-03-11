<?php

namespace Imi\Test\WebSocketServer\MainServer\Controller;

use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;

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
     * @param \stdClass $data
     *
     * @return array
     */
    public function test($data)
    {
        return ['data' => $data];
    }
}
