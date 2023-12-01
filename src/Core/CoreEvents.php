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
    public const LOAD_CONFIG = 'IMI.LOAD_CONFIG';

    /**
     * 加载运行时.
     */
    public const LOAD_RUNTIME = 'IMI.LOAD_RUNTIME';

    /**
     * 加载运行时缓存.
     */
    public const LOAD_RUNTIME_INFO = 'IMI.LOAD_RUNTIME_INFO';

    /**
     * 构建运行时缓存.
     */
    public const BUILD_RUNTIME = 'IMI.BUILD_RUNTIME';

    /**
     * 应用运行.
     */
    public const APP_RUN = 'IMI.APP_RUN';

    /**
     * 快速启动前置事件.
     */
    public const BEFORE_QUICK_START = 'IMI.QUICK_START_BEFORE';

    /**
     * 快速启动后置事件.
     */
    public const AFTER_QUICK_START = 'IMI.QUICK_START_AFTER';

    /**
     * 框架初始化.
     */
    public const INITED = 'IMI.INITED';

    /**
     * 项目初始化.
     */
    public const APP_INIT = 'IMI.APP.INIT';

    /**
     * 扫描框架.
     */
    public const SCAN_IMI = 'IMI.SCAN_IMI';

    /**
     * 扫描 vendor 组件.
     */
    public const SCAN_VENDOR = 'IMI.SCAN_VENDOR';

    /**
     * 扫描项目.
     */
    public const SCAN_APP = 'IMI.SCAN_APP';

    /**
     * 初始化 Main 类.
     */
    public const INIT_MAIN = 'IMI.INIT_MAIN';
}
