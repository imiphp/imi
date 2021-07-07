<?php

declare(strict_types=1);

namespace Imi\Pool;

class ResourceConfigMode
{
    /**
     * 轮流
     */
    public const TURN = 1;

    /**
     * 随机.
     */
    public const RANDOM = 2;

    private function __construct()
    {
    }
}
