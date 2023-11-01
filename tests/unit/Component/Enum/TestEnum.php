<?php

declare(strict_types=1);

namespace Imi\Test\Component\Enum;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

class TestEnum extends BaseEnum
{
    #[EnumItem(text: '甲')]
    public const A = 1;

    #[EnumItem(text: '乙')]
    public const B = 2;

    #[EnumItem(text: '丙')]
    public const C = 3;
}
