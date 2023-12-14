<?php

declare(strict_types=1);

namespace Imi\Model\Tree\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 提取字段中的属性到当前模型.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class TreeModel extends Base
{
    public function __construct(
        /**
         * 主键字段名；默认为null，则自动获取.
         */
        public ?string $idField = null,
        /**
         * 父级ID字段名.
         */
        public string $parentField = 'parent_id',
        /**
         * 子集字段名.
         */
        public string $childrenField = 'children'
    ) {
    }
}
