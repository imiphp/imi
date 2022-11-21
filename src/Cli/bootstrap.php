<?php

declare(strict_types=1);

namespace Imi\Cli;

use Imi\App;

return static function () {
    $path = null;

    if (!class_exists(\Imi\App::class))
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

    App::runApp($path ?? realpath(\dirname($_SERVER['SCRIPT_NAME'], 2)), \Imi\Cli\CliApp::class);
};
