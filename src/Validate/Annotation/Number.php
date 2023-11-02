<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 数值验证，允许整数和小数.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Number extends Condition
{
    public function __construct(
        public ?string $name = null,
        public bool $optional = false,
        public mixed $default = null,
        public bool $inverseResult = false,
        public string $message = '{name} validate failed',
        /**
         * 验证回调.
         *
         * @var array|callable
         */
        public $callable = '\\Imi\\Validate\\ValidatorHelper::number',
        public array $args = ['{:value}', '{min}', '{max}', '{accuracy}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 最小值，为null不限制.
         *
         * @var int|float|null
         */
        public $min = null,
        /**
         * 最大值，为null不限制.
         *
         * @var int|float|null
         */
        public $max = null,
        /**
         * 小数精度位数，为null不限制.
         */
        public ?int $accuracy = null
    ) {
    }
}
