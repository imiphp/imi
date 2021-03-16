<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Base;

class FinishEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public Base $server;

    /**
     * 任务ID.
     */
    public int $taskId = 0;

    /**
     * 任务数据.
     *
     * @var mixed
     */
    public $data;
}
