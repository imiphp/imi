<?php

declare(strict_types=1);

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAfterThrowingClass")
 */
class TestAfterThrowingClass
{
    public function testCancelThrow()
    {
        throw new \RuntimeException('test');
    }

    public function testNotCancelThrow()
    {
        throw new \RuntimeException('test');
    }
}
