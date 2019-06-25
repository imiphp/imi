<?php
namespace Imi\Test\Aop\Classes;

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
