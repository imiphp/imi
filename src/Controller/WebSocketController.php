<?php
namespace Imi\Controller;

use Imi\Util\TBeanClone;

/**
 * WebSocket 控制器
 */
abstract class WebSocketController
{
    
    use TBeanClone;
    
    /**
     * 请求
     * @var \Imi\Server\WebSocket\Server
     */
    public $server;

    /**
     * 桢
     * @var \Imi\Server\WebSocket\Message\IFrame
     */
    public $frame;
}