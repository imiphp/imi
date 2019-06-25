<?php
namespace Imi\Test\Aop\Classes;

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
