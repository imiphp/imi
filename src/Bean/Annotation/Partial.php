<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 局部类型（Partial）注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[\Imi\Bean\Annotation\Parser(className: \Imi\Bean\Parser\PartialParser::class)]
class Partial extends Base
{
    public function __construct(
        /**
         * 注入类名.
         */
        public string $class = ''
    ) {
    }
}
