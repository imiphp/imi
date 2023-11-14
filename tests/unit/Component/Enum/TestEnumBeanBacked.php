<?php

declare(strict_types=1);

namespace Imi\Test\Component\Enum;

enum TestEnumBeanBacked: string
{
    case A = 'hello';
    case B = 'imi';
}
