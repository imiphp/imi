<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Base;

class StartEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ?Base $server;
}
