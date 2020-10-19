<?php

namespace Imi\Test\Component\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Test\Component\Model\Base\TreeBase;

/**
 * @Entity
 *
 * @property int[] $list
 */
class ReferenceGetterTestModel extends TreeBase
{
    /**
     * @Column(virtual=true)
     *
     * @var int[]
     */
    protected $list = [];

    /**
     * Get the value of list.
     *
     * @return int[]
     */
    public function &getList()
    {
        return $this->list;
    }

    /**
     * Set the value of list.
     *
     * @param int[] $list
     *
     * @return self
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }
}
