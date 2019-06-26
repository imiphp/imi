<?php
namespace Imi\Test\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

class TestEnum extends BaseEnum
{
    /**
     * @EnumItem(text="甲", other="a1")
     */
    const A = 1;

    /**
     * @EnumItem(text="乙", other="b2")
     */
    const B = 2;

    /**
     * @EnumItem(text="丙", other="c3")
     */
    const C = 3;

}