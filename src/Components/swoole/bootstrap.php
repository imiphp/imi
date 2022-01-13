<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\App;
use Imi\AppContexts;
use Imi\Cli\ImiCommand;
use Imi\Event\Event;
use Imi\Util\File;

return static function () {
    $status = 0;
    \Swoole\Coroutine\run(static function () use (&$status) {
        try
        {
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
            Event::on('IMI.LOAD_RUNTIME_INFO', \Imi\Swoole\Process\Listener\LoadRuntimeListener::class, 19940000);
            Event::on('IMI.BUILD_RUNTIME', \Imi\Swoole\Process\Listener\BuildRuntimeListener::class, 19940000);
            Event::on('IMI.LOAD_RUNTIME_INFO', \Imi\Swoole\Task\Listener\LoadRuntimeListener::class, 19940000);
            Event::on('IMI.BUILD_RUNTIME', \Imi\Swoole\Task\Listener\BuildRuntimeListener::class, 19940000);

            // 运行
            App::run((static function () use ($path): string {
                $input = ImiCommand::getInput();
                $namespace = $input->getParameterOption('--app-namespace', false);
                if (false === $namespace)
                {
                    $appPath = App::get(AppContexts::APP_PATH) ?? ($path ?? realpath(\dirname($_SERVER['SCRIPT_NAME'], 2)));
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
            })(), \Imi\Swoole\SwooleApp::class);
        }
        catch (\Swoole\ExitException $e)
        {
            $status = $e->getStatus();
        }
        catch (\Throwable $th)
        {
            $status = 255;
            try
            {
                /** @var \Imi\Log\ErrorLog $errorLog */
                $errorLog = App::getBean('ErrorLog');
                $errorLog->onException($th);
            }
            catch (\Throwable $tth)
            {
                throw $th;
            }
        }
    });
    if (0 === $status)
    {
        Event::trigger('IMI.SWOOLE.MAIN_COROUTINE.AFTER');
    }
    else
    {
        exit($status);
    }
};
