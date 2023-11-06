<?php

declare(strict_types=1);

namespace Imi\Test\Component\Facade;

use Imi\Bean\Annotation\Bean;

#[Bean(name: 'FacadeA')]
class A
{
    public function add(mixed $a, mixed $b): mixed
    {
        return $a + $b;
    }
}
