<?php

declare(strict_types=1);

namespace Imi\Model\Tree\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 提取字段中的属性到当前模型.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class TreeModel extends Base
{
    /**
     * 主键字段名
     * 默认为null，则自动获取.
     *
     * @var string|null
     */
    public ?string $idField = null;

    /**
     * 父级ID字段名.
     *
     * @var string
     */
    public string $parentField = 'parent_id';

    /**
     * 子集字段名.
     *
     * @var string
     */
    public string $childrenField = 'children';
}
