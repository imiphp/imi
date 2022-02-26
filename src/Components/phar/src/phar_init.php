<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;

(function () {
    $tmpDir = is_dir('/run/shm') && is_writable('/run/shm') ? '/run/shm' : sys_get_temp_dir();
    //putenv('IMI_MACRO_LOCK_FILE_DIR=/dev/shm');
    putenv("IMI_MACRO_OUTPUT_DIR={$tmpDir}");

    \define('IMI_IN_PHAR', (bool) \Phar::running(false));
    \define('IMI_APP_ROOT', IMI_IN_PHAR ? \Phar::running() : realpath(getcwd()));
    \define('IMI_RUNNING_ROOT', realpath(getcwd()));

    require __DIR__ . '/vendor/autoload.php';

    if (IMI_IN_PHAR)
    {
        App::set(AppContexts::APP_PATH, IMI_APP_ROOT, true);
    }
})();
