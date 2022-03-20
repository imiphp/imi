<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;

ini_set('memory_limit', '512M'); // 进程内存限制
date_default_timezone_set('Asia/Shanghai'); // 默认时区设置

function app_real_root_path(): string
{
    return App::get(AppContexts::APP_PATH_PHYSICS);
}
