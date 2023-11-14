<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

// Bean
use Imi\Bean\Annotation\Bean;

if (\PHP_VERSION_ID >= 80100)
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Bean;

    use Imi\Bean\Annotation\Bean;
    use Imi\Test\Component\Enum\TestEnumBean;
    use Imi\Test\Component\Enum\TestEnumBeanBacked;

    if (!class_exists(EnumBean::class, false))
    {
        class EnumBean
        {
            protected TestEnumBean $enum1;

            protected TestEnumBeanBacked $enum2;

            public function getEnum1(): TestEnumBean
            {
                return $this->enum1;
            }

            public function getEnum2(): TestEnumBeanBacked
            {
                return $this->enum2;
            }
        }
    }
    PHP);
}
