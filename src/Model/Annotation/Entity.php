<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 实体注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Entity extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'camel';

    /**
     * 序列化时使用驼峰命名.
     */
    public bool $camel = true;

    public function __construct(?array $__data = null, bool $camel = true)
    {
        parent::__construct(...\func_get_args());
    }
}
