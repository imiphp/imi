<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Tree\Annotation\TreeModel;

/**
 * Tree.
 *
 * @Entity
 * @TreeModel
 * @Table(name="tb_tree", id={"id"})
 */
class TreeWithChildren extends Tree
{
    /**
     * 子节点集合.
     *
     * @Column(virtual=true)
     *
     * @var static[]
     */
    protected array $children = [];

    /**
     * Get 子节点集合.
     *
     * @return static[]
     */
    public function &getChildren(): array
    {
        return $this->children;
    }

    /**
     * Set 子节点集合.
     *
     * @param static[] $children
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}
