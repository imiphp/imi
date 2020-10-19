<?php

namespace Imi\Test\Component\Partial\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("PartialClassA")
 */
class PartialClassA
{
    public function test1()
    {
        return 1;
    }
}
