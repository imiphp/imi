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
    public const BEFORE_CREATE_SERVER = 'IMI.SERVER.CREATE.BEFORE';

    /**
     * 创建服务器对象后置操作.
     */
    public const AFTER_CREATE_SERVER = 'IMI.SERVER.CREATE.AFTER';

    /**
     * 创建服务器前置操作.
     */
    public const BEFORE_CREATE_SERVERS = 'IMI.SERVERS.CREATE.BEFORE';

    /**
     * 创建服务器后置操作.
     */
    public const AFTER_CREATE_SERVERS = 'IMI.SERVERS.CREATE.AFTER';

    /**
     * 工作进程启动事件.
     */
    public const WORKER_START = 'IMI.SERVER.WORKER_START';

    /**
     * 工作进程停止事件.
     */
    public const WORKER_STOP = 'IMI.SERVER.WORKER_STOP';
}
