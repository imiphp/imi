<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 序列化注解.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Serializable extends Base
{
    public function __construct(
        /**
         * 是否允许参与序列化.
         */
        public bool $allow = true
    ) {
    }
}
