<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\App;
use Imi\AppContexts;
use Imi\Cli\ImiCommand;
use Imi\Event\Event;
use Imi\Util\File;

return static function () {
    $path = null;

    if (!class_exists('Imi\App'))
    {
        (static function () use (&$path) {
            foreach ([
                $_SERVER['PWD'] ?? null,
                getcwd(),
                \dirname(__DIR__, 3),
                \dirname(__DIR__, 5), // 在非工作路径，使用绝对路径启动
            ] as $path)
            {
                if (!$path)
                {
                    continue;
                }
                $fileName = $path . '/vendor/autoload.php';
                if (is_file($fileName))
                {
                    require $fileName;

                    return;
                }
            }
            echo 'No file vendor/autoload.php', \PHP_EOL;
            exit(255);
        })();
    }

    // 事件监听
    Event::on('IMI.LOAD_RUNTIME_INFO', \Imi\Workerman\Process\Listener\LoadRuntimeListener::class, 19940000);
    Event::on('IMI.BUILD_RUNTIME', \Imi\Workerman\Process\Listener\BuildRuntimeListener::class, 19940000);

    App::runApp($path ?? realpath(\dirname($_SERVER['SCRIPT_NAME'], 2)), \Imi\Workerman\WorkermanApp::class);
};
