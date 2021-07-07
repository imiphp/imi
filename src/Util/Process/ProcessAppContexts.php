<?php

declare(strict_types=1);

namespace Imi\Util\Process;

/**
 * 进程相关的应用上下文名称定义.
 */
class ProcessAppContexts
{
    /**
     * 进程类型.
     */
    public const PROCESS_TYPE = 'process_type';

    /**
     * 进程名称.
     */
    public const PROCESS_NAME = 'process_name';

    /**
     * 主进程pid.
     */
    public const MASTER_PID = 'master_pid';

    /**
     * 当前进程脚本名称.
     */
    public const SCRIPT_NAME = 'script_name';

    private function __construct()
    {
    }
}
