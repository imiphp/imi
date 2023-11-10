<?php

declare(strict_types=1);

namespace Imi\Test\Component\RequestContextProxy;

class A
{
    public function add(mixed $a, mixed $b): mixed
    {
        return $a + $b;
    }
}
