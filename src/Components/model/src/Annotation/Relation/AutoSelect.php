<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 自动查询.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AutoSelect extends Base
{
    public function __construct(
        /**
         * 是否开启.
         */
        public bool $status = true,
        /**
         * 是否总是显示该属性；如果为true，在为null时序列化为数组或json不显示该属性.
         */
        public bool $alwaysShow = true
    ) {
    }
}
