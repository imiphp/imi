<?php

namespace Imi\Test\Component\Util\Imi;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestPropertyClass")
 */
class TestPropertyClass
{
    /**
     * @var int
     */
    protected $a = 1;

    /**
     * @var int
     */
    protected $b = 1;
}
