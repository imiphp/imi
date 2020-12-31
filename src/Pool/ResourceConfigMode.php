<?php

declare(strict_types=1);

namespace Imi\Pool;

class ResourceConfigMode
{
    /**
     * 轮流
     */
    const TURN = 1;

    /**
     * 随机.
     */
    const RANDOM = 2;

    private function __construct()
    {
    }
}
