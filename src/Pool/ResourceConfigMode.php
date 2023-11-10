<?php

declare(strict_types=1);

namespace Imi\Pool;

class ResourceConfigMode
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 轮询.
     */
    public const ROUND_ROBIN = 1;

    /**
     * 随机.
     */
    public const RANDOM = 2;
}
