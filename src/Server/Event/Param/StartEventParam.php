<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class StartEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;
}
