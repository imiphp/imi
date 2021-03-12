<?php

declare(strict_types=1);

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestBeforeClass")
 */
class TestBeforeClass
{
    public function test(int $id): int
    {
        return $id;
    }
}
