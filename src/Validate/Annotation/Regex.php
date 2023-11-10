<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 正则验证
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Regex extends Condition
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
        public string|array|null $callable = '\\Imi\\Validate\\ValidatorHelper::regex',
        public array $args = ['{:value}', '{pattern}'],
        public ?string $exception = null,
        public ?int $exCode = null,
        /**
         * 正则表达式文本.
         */
        public string $pattern = ''
    ) {
    }
}
