<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Controller;

use Imi\ConnectionContext;
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
     *
     * @WSRoute({"action": "test"})
     *
     * @param mixed $data
     */
    public function test($data): array
    {
        return ['data' => $data];
    }

    /**
     * @WSAction
     *
     * @WSRoute({"action": "contextTest"})
     */
    public function contextTest(): array
    {
        $key = 'test_remember';
        $count = 0;
        $countFun = static function () use (&$count) {
            return ++$count;
        };

        ConnectionContext::unset($key);

        $result = [];

        $result['actual-a'][] = 1;
        $result['expected-a'][] = ConnectionContext::remember($key, $countFun);
        $result['actual-b'][] = 1;
        $result['expected-b'][] = $count;

        $result['actual-a'][] = 1;
        $result['expected-a'][] = ConnectionContext::remember($key, $countFun);
        $result['actual-b'][] = 1;
        $result['expected-b'][] = $count;

        ConnectionContext::unset($key);

        $result['actual-a'][] = 2;
        $result['expected-a'][] = ConnectionContext::remember($key, $countFun);
        $result['actual-b'][] = 2;
        $result['expected-b'][] = $count;

        return $result;
    }
}
