<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Enum;

enum ConnectionStatus
{
    /**
     * 可用.
     */
    case Available;

    /**
     * 不可用.
     */
    case Unavailable;

    /**
     * 等待释放.
     */
    case WaitRelease;
}
