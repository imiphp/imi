<?php

namespace Imi\Util\Process;

/**
 * 进程相关的应用上下文名称定义.
 */
abstract class ProcessAppContexts
{
    /**
     * 进程类型.
     */
    const PROCESS_TYPE = 'process_type';

    /**
     * 进程名称.
     */
    const PROCESS_NAME = 'process_name';

    /**
     * 主进程pid.
     */
    const MASTER_PID = 'master_pid';

    /**
     * 当前进程脚本名称.
     */
    const SCRIPT_NAME = 'script_name';
}
