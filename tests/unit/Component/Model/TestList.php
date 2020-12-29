<?php

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Test\Component\Model\Base\TestListBase;

/**
 * tb_test_list.
 *
 * @Inherit
 *
 * @property array $list
 */
class TestList extends TestListBase
{
    /**
     * list.
     *
     * @Column(name="list", type="list", listSeparator=",", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $list;
}
