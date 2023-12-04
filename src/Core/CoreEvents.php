<?php

declare(strict_types=1);

namespace Imi\Core;

use Imi\Util\Traits\TStaticClass;

final class CoreEvents
{
    use TStaticClass;

    /**
     * 加载配置.
     */
    public const LOAD_CONFIG = 'imi.load_config';

    /**
     * 加载运行时.
     */
    public const LOAD_RUNTIME = 'imi.load_runtime';

    /**
     * 加载运行时缓存.
     */
    public const LOAD_RUNTIME_INFO = 'imi.load_runtime_info';

    /**
     * 构建运行时缓存.
     */
    public const BUILD_RUNTIME = 'imi.build_runtime';

    /**
     * 应用运行.
     */
    public const APP_RUN = 'imi.app_run';

    /**
     * 快速启动前置事件.
     */
    public const BEFORE_QUICK_START = 'imi.quick_start_before';

    /**
     * 快速启动后置事件.
     */
    public const AFTER_QUICK_START = 'imi.quick_start_after';

    /**
     * 框架初始化.
     */
    public const INITED = 'imi.inited';

    /**
     * 项目初始化.
     */
    public const APP_INIT = 'imi.app.init';

    /**
     * 扫描框架.
     */
    public const SCAN_IMI = 'imi.scan_imi';

    /**
     * 扫描 vendor 组件.
     */
    public const SCAN_VENDOR = 'imi.scan_vendor';

    /**
     * 扫描项目.
     */
    public const SCAN_APP = 'imi.scan_app';

    /**
     * 初始化 Main 类.
     */
    public const INIT_MAIN = 'imi.init_main';
}
