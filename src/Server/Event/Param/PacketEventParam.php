<?php

declare(strict_types=1);

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Base;

class PacketEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;

    /**
     * 数据.
     *
     * @var string
     */
    public string $data;

    /**
     * 客户端信息.
     *
     * @var array
     */
    public array $clientInfo;
}
