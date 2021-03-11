<?php

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestBeforeClass")
 */
class TestBeforeClass
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
