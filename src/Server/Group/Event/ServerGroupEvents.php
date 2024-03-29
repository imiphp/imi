<?php

declare(strict_types=1);

namespace Imi\Server\Group\Event;

use Imi\Util\Traits\TStaticClass;

final class ServerGroupEvents
{
    use TStaticClass;

    /**
     * 加入服务器逻辑分组事件.
     */
    public const JOIN_GROUP = 'imi.server.group.join';

    /**
     * 离开服务器逻辑分组事件.
     */
    public const LEAVE_GROUP = 'imi.server.group.leave';
}
