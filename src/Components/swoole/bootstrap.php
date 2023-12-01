<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\App;
use Imi\Core\CoreEvents;
use Imi\Core\Runtime\Event\BuildRuntimeInfoEvent;
use Imi\Core\Runtime\Event\LoadRuntimeInfoEvent;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Swoole\Event\SwooleEvents;

return static function (): void {
    $status = 0;
    \Swoole\Coroutine\run(static function () use (&$status): void {
        try
        {
            $path = null;

            if (\defined('IMI_IN_PHAR') && IMI_IN_PHAR)
            {
                $path = \dirname(__DIR__, 3);
            }
            elseif (!class_exists(\Imi\App::class))
            {
                (static function () use (&$path): void {
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
            Event::on(CoreEvents::LOAD_RUNTIME_INFO, static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Swoole\Process\Listener\LoadRuntimeListener::class)->handle($e), 19940000);
            Event::on(CoreEvents::BUILD_RUNTIME, static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Swoole\Process\Listener\BuildRuntimeListener::class)->handle($e), 19940000);
            Event::on(CoreEvents::LOAD_RUNTIME_INFO, static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Swoole\Task\Listener\LoadRuntimeListener::class)->handle($e), 19940000);
            Event::on(CoreEvents::BUILD_RUNTIME, static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Swoole\Task\Listener\BuildRuntimeListener::class)->handle($e), 19940000);

            // 运行
            App::runApp($path ?? realpath(\dirname($_SERVER['SCRIPT_NAME'], 2)), \Imi\Swoole\SwooleApp::class);
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
                Log::error($th);
            }
            catch (\Throwable $tth)
            {
                throw $th;
            }
        }
        Event::dispatch(eventName: SwooleEvents::MAIN_COROUTINE_END);
    });
    if (0 === $status)
    {
        Event::dispatch(eventName: SwooleEvents::MAIN_COROUTINE_AFTER);
    }
    else
    {
        exit($status);
    }
};
