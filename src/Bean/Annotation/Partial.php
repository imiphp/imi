<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 局部类型（Partial）注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\PartialParser")
 */
class Partial extends Base
{
    /**
     * 注入类名.
     *
     * @var string
     */
    public string $class;

    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'class';
}
