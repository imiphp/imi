<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Controller;

use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;

/**
 * 数据收发测试.
 */
#[WSController(route: '/test')]
class Test2Controller extends WebSocketController
{
    /**
     * @param mixed $data
     */
    #[WSAction]
    #[WSRoute(condition: ['action' => 'test'])]
    public function test($data): array
    {
        return ['data' => $data];
    }
}
