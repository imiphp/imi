<?php

declare(strict_types=1);

namespace Imi\Test\Component\Facade;

use Imi\Bean\Annotation\Bean;

#[Bean(name: 'FacadeA')]
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
