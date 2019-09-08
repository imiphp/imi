<?php
namespace Imi\Model\Tree\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 提取字段中的属性到当前模型
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class TreeModel extends Base
{
    /**
     * 父级ID字段名
     *
     * @var string
     */
    public $parentField = 'parent_id';

    /**
     * 子集字段名
     *
     * @var string
     */
    public $childrenField = 'children';

}
