<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 类事件监听.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[\Imi\Bean\Annotation\Parser(className: \Imi\Bean\Parser\ClassEventParser::class)]
class ClassEventListener extends Base
{
    public function __construct(
        /**
         * 类名.
         */
        public string $className = '',
        /**
         * 事件名.
         */
        public string $eventName = '',
        /**
         * 优先级，越大越先执行.
         */
        public int $priority = 0
    ) {
    }
}
