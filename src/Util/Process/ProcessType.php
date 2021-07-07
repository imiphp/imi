<?php

declare(strict_types=1);

namespace Imi\Util\Process;

/**
 * 进程类型.
 */
class ProcessType
{
    /**
     * master 进程.
     */
    public const MASTER = 'master';

    /**
     * manager 进程.
     */
    public const MANAGER = 'manager';

    /**
     * worker 进程.
     */
    public const WORKER = 'worker';

    /**
     * task worker 进程.
     */
    public const TASK_WORKER = 'task_worker';

    /**
     * 进程.
     */
    public const PROCESS = 'process';

    private function __construct()
    {
    }
}
