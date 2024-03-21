<?php

declare(strict_types=1);

namespace Imi\Test\Component\Aop\Classes;

class TestPriorityClass
{
    public function test(array &$list): int
    {
        $list[] = 0;

        return 0;
    }
}
