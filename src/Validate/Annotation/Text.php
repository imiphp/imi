<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 文本验证
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Text extends Condition
{
    public function __construct(
        public ?string $name = null,
        public bool $optional = false,
        public mixed $default = null,
        public bool $inverseResult = false,
        public string $message = '{name} validate failed',
        /**
         * 验证回调.
         */
        public array|callable $callable = '\\Imi\\Validate\\ValidatorHelper::text',
        public array $args = ['{:value}', '{min}', '{max}', '{char}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 是否为字符模式，默认为 false；设为 true 则使用字符判断长度；设为 false 则使用字节判断长度.
         */
        public bool $char = false,
        /**
         * 最短长度.
         */
        public int $min = 0,
        /**
         * 最长长度，为null则不限制长度.
         */
        public ?int $max = null
    ) {
    }
}
