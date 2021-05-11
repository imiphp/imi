<?php

namespace Imi\AC\Model\Filter;

use Imi\AC\Model\Operation;
use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;

/**
 * OperationTreeItem.
 *
 * @Inherit
 *
 * @property \Imi\AC\Model\Filter\OperationTreeItem[] $children
 */
class OperationTreeItem extends Operation
{
    /**
     * 子操作列表.
     *
     * @Column(virtual=true)
     *
     * @var \Imi\AC\Model\Filter\OperationTreeItem[]
     */
    protected $children = [];

    /**
     * Get 子操作列表.
     *
     * @return \Imi\AC\Model\Filter\OperationTreeItem[]
     */
    public function &getChildren()
    {
        return $this->children;
    }

    /**
     * Set 子操作列表.
     *
     * @param \Imi\AC\Model\Filter\OperationTreeItem[] $children 子操作列表
     *
     * @return self
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }
}
