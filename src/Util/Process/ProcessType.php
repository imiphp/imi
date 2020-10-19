<?php

namespace Imi\Util\Process;

/**
 * 进程类型.
 */
abstract class ProcessType
{
    /**
     * master 进程.
     */
    const MASTER = 'master';

    /**
     * manager 进程.
     */
    const MANAGER = 'manager';

    /**
     * worker 进程.
     */
    const WORKER = 'worker';

    /**
     * task worker 进程.
     */
    const TASK_WORKER = 'task_worker';

    /**
     * 进程.
     */
    const PROCESS = 'process';
}
