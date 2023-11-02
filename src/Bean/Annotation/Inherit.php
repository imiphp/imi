<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定允许继承父类的指定注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS_CONSTANT)]
class Inherit extends Base
{
    public function __construct(
        /**
         * 允许的注解类，为 null 则不限制，支持字符串或数组.
         *
         * @var string|string[]|null
         */
        public $annotation = null
    ) {
    }
}
