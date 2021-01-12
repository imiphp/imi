<?php

declare(strict_types=1);

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Base;

class StartEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base|null
     */
    public ?Base $server;
}
