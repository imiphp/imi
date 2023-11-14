<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

// Bean
use Imi\Bean\Annotation\Bean;
use Imi\Test\Component\Enum\TestEnumBean;
use Imi\Test\Component\Enum\TestEnumBeanBacked;

#[
    Bean(name: 'EnumBean1'),
    Bean(name: 'EnumBean2'),
]
class EnumBean
{
    protected TestEnumBean $enum1;

    protected TestEnumBeanBacked $enum2;

    protected TestEnumBean|TestEnumBeanBacked $enum3;

    public function getEnum1(): TestEnumBean
    {
        return $this->enum1;
    }

    public function getEnum2(): TestEnumBeanBacked
    {
        return $this->enum2;
    }

    public function getEnum3(): TestEnumBean|TestEnumBeanBacked
    {
        return $this->enum3;
    }
}
