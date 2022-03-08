<?php

declare(strict_types=1);

namespace Imi;

class AppContexts
{
    /**
     * 应用命名空间根所在路径.
     */
    public const APP_PATH = 'app_path';

    /**
     * 应用命名空间根所在物理路径.
     */
    public const APP_PATH_PHYSICS = 'app_path_physics';

    private function __construct()
    {
    }
}
