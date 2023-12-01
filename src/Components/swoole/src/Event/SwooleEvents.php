<?php

declare(strict_types=1);

namespace Imi\Swoole\Event;

use Imi\Util\Traits\TStaticClass;

final class SwooleEvents
{
    use TStaticClass;

    /**
     * Swoole 主协程执行完毕事件，此事件在协程中.
     */
    public const MAIN_COROUTINE_END = 'IMI.SWOOLE.MAIN_COROUTINE.END';

    /**
     * Swoole 主协程执行完毕后置事件，此事件在非协程中.
     */
    public const MAIN_COROUTINE_AFTER = 'IMI.SWOOLE.MAIN_COROUTINE.AFTER';

    /**
     * Swoole 服务器开始前.
     */
    public const BEFORE_SERVER_START = 'IMI.SWOOLE.SERVER.BEFORE_START';

    /**
     * 在工作进程中执行的应用初始化.
     */
    public const WORKER_APP_START = 'IMI.MAIN_SERVER.WORKER.START.APP';

    /**
     * 自定义进程池中的进程开始.
     */
    public const PROCESS_POOL_PROCESS_BEGIN = 'IMI.PROCESS_POOL.PROCESS.BEGIN';

    /**
     * 自定义进程池中的进程结束.
     */
    public const PROCESS_POOL_PROCESS_END = 'IMI.PROCESS_POOL.PROCESS.END';

    /**
     * Swoole Server 事件：OnStart.
     */
    public const SERVER_START = 'IMI.MAIN_SERVER.START';

    /**
     * Swoole Server 事件：OnShutdown.
     */
    public const SERVER_SHUTDOWN = 'IMI.MAIN_SERVER.SHUTDOWN';

    /**
     * Swoole Server 事件：OnWorkerStart.
     */
    public const SERVER_WORKER_START = 'IMI.MAIN_SERVER.WORKER.START';

    /**
     * Swoole Server 事件：OnWorkerStop.
     */
    public const SERVER_WORKER_STOP = 'IMI.MAIN_SERVER.WORKER.STOP';

    /**
     * Swoole Server 事件：OnManagerStart.
     */
    public const SERVER_MANAGER_START = 'IMI.MAIN_SERVER.MANAGER.START';

    /**
     * Swoole Server 事件：OnManagerStop.
     */
    public const SERVER_MANAGER_STOP = 'IMI.MAIN_SERVER.MANAGER.STOP';

    /**
     * Swoole Server 事件：OnTask.
     */
    public const SERVER_TASK = 'IMI.MAIN_SERVER.TASK';

    /**
     * Swoole Server 事件：OnFinish.
     */
    public const SERVER_FINISH = 'IMI.MAIN_SERVER.FINISH';

    /**
     * Swoole Server 事件：OnPipeMessage.
     */
    public const SERVER_PIPE_MESSAGE = 'IMI.MAIN_SERVER.PIPE_MESSAGE';

    /**
     * Swoole Server 事件：OnWorkerError.
     */
    public const SERVER_WORKER_ERROR = 'IMI.MAIN_SERVER.WORKER_ERROR';

    /**
     * Swoole Server 事件：OnWorkerExit.
     */
    public const SERVER_WORKER_EXIT = 'IMI.MAIN_SERVER.WORKER.EXIT';
}
