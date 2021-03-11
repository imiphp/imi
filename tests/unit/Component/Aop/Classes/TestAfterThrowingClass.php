<?php

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAfterThrowingClass")
 */
class TestAfterThrowingClass
{
    /**
     * @return void
     */
    public function testCancelThrow()
    {
        throw new \RuntimeException('test');
    }

    /**
     * @return void
     */
    public function testNotCancelThrow()
    {
        throw new \RuntimeException('test');
    }
}
