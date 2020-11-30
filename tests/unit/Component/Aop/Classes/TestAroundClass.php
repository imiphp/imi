<?php

declare(strict_types=1);

namespace Imi\Test\Component\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAroundClass")
 */
class TestAroundClass
{
    public function test(int $id)
    {
        return $id;
    }
}
