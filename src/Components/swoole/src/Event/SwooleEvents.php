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
    public const MAIN_COROUTINE_END = 'imi.swoole.main_coroutine.end';

    /**
     * Swoole 主协程执行完毕后置事件，此事件在非协程中.
     */
    public const MAIN_COROUTINE_AFTER = 'imi.swoole.main_coroutine.after';

    /**
     * Swoole 服务器开始前.
     */
    public const BEFORE_SERVER_START = 'imi.swoole.server.before_start';

    /**
     * 在工作进程中执行的应用初始化.
     */
    public const WORKER_APP_START = 'imi.main_server.worker.start.app';

    /**
     * 自定义进程池中的进程开始.
     */
    public const PROCESS_POOL_PROCESS_BEGIN = 'imi.process_pool.process.begin';

    /**
     * 自定义进程池中的进程结束.
     */
    public const PROCESS_POOL_PROCESS_END = 'imi.process_pool.process.end';

    /**
     * Swoole Server 事件：OnStart.
     */
    public const SERVER_START = 'imi.main_server.start';

    /**
     * Swoole Server 事件：OnShutdown.
     */
    public const SERVER_SHUTDOWN = 'imi.main_server.shutdown';

    /**
     * Swoole Server 事件：OnWorkerStart.
     */
    public const SERVER_WORKER_START = 'imi.main_server.worker.start';

    /**
     * Swoole Server 事件：OnWorkerStop.
     */
    public const SERVER_WORKER_STOP = 'imi.main_server.worker.stop';

    /**
     * Swoole Server 事件：OnManagerStart.
     */
    public const SERVER_MANAGER_START = 'imi.main_server.manager.start';

    /**
     * Swoole Server 事件：OnManagerStop.
     */
    public const SERVER_MANAGER_STOP = 'imi.main_server.manager.stop';

    /**
     * Swoole Server 事件：OnTask.
     */
    public const SERVER_TASK = 'imi.main_server.task';

    /**
     * Swoole Server 事件：OnFinish.
     */
    public const SERVER_FINISH = 'imi.main_server.finish';

    /**
     * Swoole Server 事件：OnPipeMessage.
     */
    public const SERVER_PIPE_MESSAGE = 'imi.main_server.pipe_message';

    /**
     * Swoole Server 事件：OnWorkerError.
     */
    public const SERVER_WORKER_ERROR = 'imi.main_server.worker_error';

    /**
     * Swoole Server 事件：OnWorkerExit.
     */
    public const SERVER_WORKER_EXIT = 'imi.main_server.worker.exit';
}
