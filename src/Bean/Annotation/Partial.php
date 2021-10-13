<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 局部类型（Partial）注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\PartialParser")
 *
 * @property string $class 注入类名
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Partial extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'class';

    public function __construct(?array $__data = null, string $class = '')
    {
        parent::__construct(...\func_get_args());
    }
}
