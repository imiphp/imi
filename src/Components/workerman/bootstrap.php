<?php

declare(strict_types=1);

use Imi\App;
use Imi\AppContexts;
use Imi\Event\Event;
use Imi\Util\File;
use Symfony\Component\Console\Input\ArgvInput;

return function () {
    $path = null;

    if (!class_exists('Imi\App'))
    {
        (function () use (&$path) {
            foreach ([
                $_SERVER['PWD'],
                getcwd(),
                dirname(__DIR__, 3),
                dirname(__DIR__, 5), // 在非工作路径，使用绝对路径启动
            ] as $path)
            {
                $fileName = $path . '/vendor/autoload.php';
                if (is_file($fileName))
                {
                    break;
                }
            }
            if (!is_file($fileName))
            {
                echo 'No file vendor/autoload.php', \PHP_EOL;
                exit(255);
            }
            require $fileName;
        })();
    }

    // 事件监听
    Event::on('IMI.LOAD_RUNTIME_INFO', \Imi\Workerman\Process\Listener\LoadRuntimeListener::class, 19940000);
    Event::on('IMI.BUILD_RUNTIME', \Imi\Workerman\Process\Listener\BuildRuntimeListener::class, 19940000);

    App::run((function () use ($path): string {
        $input = new ArgvInput();
        $namespace = $input->getParameterOption('--app-namespace');
        if (false === $namespace)
        {
            $appPath = App::get(AppContexts::APP_PATH) ?? ($path ?? dirname($_SERVER['SCRIPT_NAME'], 2));
            $config = include File::path($appPath, 'config/config.php');
            if (!isset($config['namespace']))
            {
                echo 'Has no namespace, please add arg: --app-namespace "Your App Namespace"', \PHP_EOL;
                exit(255);
            }
            App::setNx(AppContexts::APP_PATH, $appPath, true);
            $namespace = $config['namespace'];
        }

        return $namespace;
    })(), \Imi\Workerman\WorkermanApp::class);
};
