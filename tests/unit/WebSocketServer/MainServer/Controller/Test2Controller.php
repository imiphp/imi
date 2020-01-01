<?php
namespace Imi\Test\WebSocketServer\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;
use Imi\Event\Event;
use Imi\RequestContext;

/**
 * 数据收发测试
 * @WSController(route="/test")
 */
class Test2Controller extends WebSocketController
{
    /**
     * @WSAction
     * @WSRoute({"action"="test"})
     * @param 
     * @return void
     */
    public function test($data)
    {
        return ['data'=>$data];
    }

}