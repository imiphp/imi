<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;
use Imi\RoadRunner\RoadRunnerApp;
use Imi\Cli\ImiCommand;
use Imi\Event\Event;
use Imi\Util\File;

//require_once dirname(__DIR__) . '/vendor/autoload.php';
//
//App::set(AppContexts::APP_PATH, dirname(__DIR__), true);
//App::run('ImiApp', RoadRunnerApp::class);

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

    App::run('ImiApp', RoadRunnerApp::class);
};
