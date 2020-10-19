<?php

namespace Imi\Test\Component\Facade;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("FacadeA")
 */
class A
{
    public function add($a, $b)
    {
        return $a + $b;
    }
}
