<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定注解类处理器.
 *
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute]
class Parser extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'className';

    /**
     * 处理器类名.
     *
     * @var string
     */
    public string $className = '';

    public function __construct(?array $__data = null, string $className = '')
    {
        parent::__construct(...\func_get_args());
    }
}
