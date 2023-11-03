<?php

declare(strict_types=1);

namespace Imi\Test\Component\RequestContextProxy;

class A
{
    /**
     * @param mixed $a
     * @param mixed $b
     */
    public function add($a, $b): mixed
    {
        return $a + $b;
    }
}
