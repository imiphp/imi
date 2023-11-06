<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 整数验证
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Integer extends Condition
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
        public array|callable $callable = '\\Imi\\Validate\\ValidatorHelper::int',
        public array $args = ['{:value}', '{min}', '{max}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 最小值，为null不限制.
         */
        public ?int $min = null,
        /**
         * 最大值，为null不限制.
         */
        public ?int $max = null
    ) {
    }
}
