<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Process\Process;
use Swoole\Coroutine\Server\Connection;

class PipeMessageEventParam extends EventParam
{
    /**
     * 当前进程.
     */
    public ?Process $process = null;

    /**
     * 动作名.
     */
    public string $action = '';

    /**
     * 数据，可以是任意PHP类型.
     */
    public mixed $data = null;

    /**
     * 连接对象，仅其它进程发给当前进程才有.
     */
    public ?Connection $connection = null;
}
