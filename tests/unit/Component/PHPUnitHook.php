<?php

declare(strict_types=1);

namespace Imi\Test\Component;

use Imi\App;
use Imi\Cli\CliApp;
use Imi\Core\CoreEvents;
use Imi\Event\Contract\IEvent;
use Imi\Event\Event;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class PHPUnitHook implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        Event::on(CoreEvents::APP_RUN, static function (IEvent $param): void {
            $param->stopPropagation();
            if (file_exists($file = __DIR__ . '/.runtime/test'))
            {
                shell_exec("rm -rf {$file}");
            }
        }, 1);
        try
        {
            App::run('Imi\Test\Component', CliApp::class, static function (): void {
            });
        }
        catch (\Throwable $th)
        {
            var_dump((string) $th); // 方便错误调试查看
            throw $th;
        }
    }
}
