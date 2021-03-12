<?php

declare(strict_types=1);

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAfterClass")
 */
class TestAfterClass
{
    public function test(int $id): int
    {
        return $id;
    }
}
