<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 比较验证
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Compare extends Condition
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
        public array|callable $callable = '\\Imi\\Validate\\ValidatorHelper::compare',
        public array $args = ['{:value}', '{operation}', '{value}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 被比较值
         */
        public mixed $value = null,
        /**
         * 比较符，使用顺序：name代表的值->比较符->被比较值；允许使用：==、!=、===、!==、<、<=、>、>=.
         */
        public string $operation = '=='
    ) {
    }
}
