<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 指定注解类处理器.
 *
 * @Annotation
 * @Target("CLASS")
 */
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
}
