<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;

(function () {
    //putenv('IMI_MACRO_LOCK_FILE_DIR=/dev/shm');
    putenv('IMI_MACRO_OUTPUT_DIR=' . (is_dir('/run/shm') && is_writable('/run/shm') ? '/run/shm' : sys_get_temp_dir()));

    /**
     * 是否运行在 phar 模式.
     */
    \define('IMI_IN_PHAR', (bool) \Phar::running(false));

    /**
     * 工作路径.
     */
    \define('IMI_RUNNING_ROOT', realpath(getcwd()));

    /**
     * 项目路径.
     * 有可能在 phar 路径中.
     */
    \define('IMI_PHAR_APP_ROOT', IMI_IN_PHAR ? \Phar::running() : IMI_RUNNING_ROOT);

    require __DIR__ . '/vendor/autoload.php';

    if (IMI_IN_PHAR)
    {
        App::set(AppContexts::APP_PATH, IMI_PHAR_APP_ROOT, true);
    }
})();
