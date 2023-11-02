<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 方法参数注入.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class InjectArg extends Base
{
    public function __construct(
        /**
         * 参数名.
         */
        public string $name = '',
        /**
         * 注入的值
         *
         * @var mixed
         */
        public $value = null
    ) {
    }
}
