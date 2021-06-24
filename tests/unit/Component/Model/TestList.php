<?php

declare(strict_types=1);

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
     * @var array
     */
    protected $list;

    /**
     * 获取 list.
     *
     * @phpstan-ignore-next-line
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * 赋值 list.
     *
     * @phpstan-ignore-next-line
     *
     * @param array $list list
     *
     * @return static
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }
}
