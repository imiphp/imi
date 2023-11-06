<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 列表验证
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class InList extends Condition
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
        public array|callable $callable = '\\Imi\\Validate\\ValidatorHelper::in',
        public array $args = ['{:value}', '{list}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 列表.
         */
        public array $list = []
    ) {
    }
}
