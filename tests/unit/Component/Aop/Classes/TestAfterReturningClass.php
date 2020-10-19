<?php

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAfterReturningClass")
 */
class TestAfterReturningClass
{
    public function test(int $id)
    {
        return $id;
    }
}
