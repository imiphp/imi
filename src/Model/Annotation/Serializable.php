<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 序列化注解.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Serializable extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'allow';

    /**
     * 是否允许参与序列化.
     */
    public bool $allow = true;

    public function __construct(?array $__data = null, bool $allow = true)
    {
        parent::__construct(...\func_get_args());
    }
}
