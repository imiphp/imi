<?php
namespace Imi\Test\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestAfterClass")
 */
class TestAfterClass
{
    public function test(int $id)
    {
        return $id;
    }

}
