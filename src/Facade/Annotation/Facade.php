<?php

declare(strict_types=1);

namespace Imi\Facade\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 门面定义.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Facade extends Base
{
    public function __construct(
        /**
         * 类名，支持 Bean 名.
         */
        public string $class = '',
        /**
         * 为 true 时，使用当前请求上下文的 Bean 对象
         */
        public bool $request = false,
        /**
         * 实例化参数.
         */
        public array $args = []
    ) {
    }
}
