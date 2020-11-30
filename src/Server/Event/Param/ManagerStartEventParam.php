<?php

declare(strict_types=1);

namespace Imi\Server\Event\Param;

use Imi\Server\Base;
use Imi\Event\EventParam;

class ManagerStartEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;
}
