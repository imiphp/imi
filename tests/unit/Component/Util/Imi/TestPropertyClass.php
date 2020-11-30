<?php

declare(strict_types=1);

namespace Imi\Test\Component\Util\Imi;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestPropertyClass")
 */
class TestPropertyClass
{
    protected $a = 1;

    protected $b = 1;
}
