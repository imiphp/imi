<?php

declare(strict_types=1);

namespace Imi\Server\Event;

use Imi\Util\Traits\TStaticClass;

final class ServerEvents
{
    use TStaticClass;

    /**
     * 创建服务器对象前置操作.
     */
    public const BEFORE_CREATE_SERVER = 'imi.server.create.before';

    /**
     * 创建服务器对象后置操作.
     */
    public const AFTER_CREATE_SERVER = 'imi.server.create.after';

    /**
     * 创建服务器前置操作.
     */
    public const BEFORE_CREATE_SERVERS = 'imi.servers.create.before';

    /**
     * 创建服务器后置操作.
     */
    public const AFTER_CREATE_SERVERS = 'imi.servers.create.after';

    /**
     * 服务器启动前.
     */
    public const BEFORE_SERVER_START = 'imi.server.before_start';

    /**
     * 工作进程启动事件.
     */
    public const WORKER_START = 'imi.server.worker_start';

    /**
     * 在工作进程中执行的应用初始化.
     */
    public const WORKER_APP_START = 'imi.server.worker.app.start';

    /**
     * 工作进程停止事件.
     */
    public const WORKER_STOP = 'imi.server.worker_stop';
}
