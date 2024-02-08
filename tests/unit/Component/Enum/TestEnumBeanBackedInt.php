<?php

declare(strict_types=1);

namespace Imi\Test\Component\Enum;

if (\PHP_VERSION_ID >= 80100 && !enum_exists(TestEnumBeanBackedInt::class, false))
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Enum;
    enum TestEnumBeanBackedInt: int {
        case A = 1;
        case B = 2;
    }
    PHP);
}
