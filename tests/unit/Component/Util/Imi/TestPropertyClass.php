<?php

declare(strict_types=1);

namespace Imi\Test\Component\Util\Imi;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestPropertyClass")
 */
class TestPropertyClass
{
    protected int $a = 1;

    protected int $b = 1;
}
