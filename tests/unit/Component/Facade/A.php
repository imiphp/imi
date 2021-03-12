<?php

declare(strict_types=1);

namespace Imi\Test\Component\Facade;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("FacadeA")
 */
class A
{
    /**
     * @param mixed $a
     * @param mixed $b
     *
     * @return mixed
     */
    public function add($a, $b)
    {
        return $a + $b;
    }
}
