<?php

namespace Imi\Server\Route\Annotation\WebSocket;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 配置注解
 * 写在 http 控制器的动作方法.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\ControllerParser")
 */
class WSConfig extends Base
{
    /**
     * 处理器类.
     *
     * @var string
     */
    public $parserClass;

    /**
     * 该动作仅作为 websocket 动作，握手失败则返回 400 错误.
     *
     * @var bool
     */
    public $wsOnly = true;
}
