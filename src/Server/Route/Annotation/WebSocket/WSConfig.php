<?php
namespace Imi\Server\Route\Annotation\WebSocket;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 配置注解
 * 写在 http 控制器的动作方法
 * 
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\ControllerParser")
 */
class WSConfig extends Base
{
    /**
     * 处理器类
     * @var string
     */
    public $parserClass;
}