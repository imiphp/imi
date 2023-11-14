<?php

declare(strict_types=1);

namespace Imi\Test\Component\Enum;

if (\PHP_VERSION_ID >= 80100 && !enum_exists(TestEnumBeanBacked::class, false))
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Enum;
    enum TestEnumBeanBacked: string {
        case A = 'hello';
        case B = 'imi';
    }
    PHP);
}
