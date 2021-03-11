<?php

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAroundClass")
 */
class TestAroundClass
{
    /**
     * @param int $id
     *
     * @return int
     */
    public function test(int $id)
    {
        return $id;
    }
}
