<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Base;

class PacketEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Swoole\Server\Base
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
