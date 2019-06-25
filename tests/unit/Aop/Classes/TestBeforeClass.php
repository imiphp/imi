<?php
namespace Imi\Test\Aop\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestBeforeClass")
 */
class TestBeforeClass
{
    public function test(int $id)
    {
        return $id;
    }

}
