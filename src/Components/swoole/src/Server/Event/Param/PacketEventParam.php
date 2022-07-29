<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Contract\ISwooleServer;

class PacketEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ?ISwooleServer $server = null;

    /**
     * 数据.
     */
    public string $data = '';

    /**
     * 客户端信息.
     */
    public array $clientInfo = [];
}
