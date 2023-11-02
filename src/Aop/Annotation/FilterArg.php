<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 过滤方法参数注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class FilterArg extends Base
{
    public function __construct(
        /**
         * 参数名.
         */
        public ?string $name = null,
        /**
         * 过滤器.
         *
         * @var ?callable
         */
        public $filter = null
    ) {
    }
}
