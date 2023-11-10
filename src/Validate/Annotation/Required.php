<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

/**
 * 必选参数.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Required extends Condition
{
    public function __construct(public ?string $name = null, public bool $optional = false, public mixed $default = null, public bool $inverseResult = false, public string $message = '{name} validate failed', public string|array|null $callable = '\\Imi\\Util\\ObjectArrayHelper::exists', public array $args = ['{:data}', '{name}'], public ?string $exception = null, public ?int $exCode = null)
    {
    }
}
