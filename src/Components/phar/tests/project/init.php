<?php
ini_set('memory_limit', '512M'); // 进程内存限制
date_default_timezone_set('Asia/Shanghai'); // 默认时区设置

function app_real_root_path(): string
{
    // todo 临时解决启动入口问题
    return \dirname(realpath($_SERVER['SCRIPT_FILENAME']));
    // return App::get(AppContexts::APP_PATH_PHYSICS);
}
