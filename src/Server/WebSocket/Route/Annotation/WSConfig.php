<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 配置注解
 * 写在 http 控制器的动作方法.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
#[Parser(className: \Imi\Server\Http\Parser\ControllerParser::class)]
class WSConfig extends Base
{
    public function __construct(
        /**
         * 处理器类.
         */
        public ?string $parserClass = null,
        /**
         * 该动作仅作为 websocket 动作，握手失败则返回 400 错误.
         */
        public bool $wsOnly = true
    ) {
    }
}
