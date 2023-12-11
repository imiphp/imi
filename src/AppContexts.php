<?php

declare(strict_types=1);

namespace Imi;

class AppContexts
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 应用命名空间根所在路径.
     */
    public const APP_PATH = 'app_path';

    /**
     * 应用命名空间根所在物理路径.
     */
    public const APP_PATH_PHYSICS = 'app_path_physics';

    /**
     * 事件调度器.
     */
    public const EVENT_DISPATCHER = 'event_dispatcher';

    /**
     * 事件监听提供者.
     */
    public const EVENT_LISTENER_PROVIDER = 'event_listener_provider';
}
