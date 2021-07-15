<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定注解类处理器.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @property string $className 处理器类名
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Parser extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'className';

    public function __construct(?array $__data = null, string $className = '')
    {
        parent::__construct(...\func_get_args());
    }
}
