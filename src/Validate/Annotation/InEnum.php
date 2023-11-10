<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 枚举验证
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class InEnum extends Condition
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
        public string|array|null $callable = '\\Imi\\Validate\\ValidatorHelper::inEnum',
        public array $args = ['{:value}', '{enum}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 注解类名.
         */
        public string $enum = ''
    ) {
    }
}
