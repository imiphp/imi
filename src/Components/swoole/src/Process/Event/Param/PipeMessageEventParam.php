<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Process\Event\ProcessEvents;
use Imi\Swoole\Process\Process;
use Swoole\Coroutine\Server\Connection;

class PipeMessageEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 当前进程.
         */
        public readonly ?Process $process = null,
        /**
         * 动作名.
         */
        public readonly string $action = '',
        /**
         * 数据，可以是任意PHP类型.
         */
        public readonly mixed $data = null,
        /**
         * 连接对象，仅其它进程发给当前进程才有.
         */
        public readonly ?Connection $connection = null
    ) {
        parent::__construct(ProcessEvents::PIPE_MESSAGE, $process);
    }
}
