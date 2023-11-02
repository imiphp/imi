<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定注解类处理器.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Parser extends Base
{
    public function __construct(
        /**
         * 处理器类名.
         */
        public string $className = ''
    ) {
    }
}
