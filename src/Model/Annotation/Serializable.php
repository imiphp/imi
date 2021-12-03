<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 序列化注解.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property bool $allow 是否允许参与序列化
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Serializable extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'allow';

    public function __construct(?array $__data = null, bool $allow = true)
    {
        parent::__construct(...\func_get_args());
    }
}
