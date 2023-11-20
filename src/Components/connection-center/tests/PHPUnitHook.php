<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test;

use Imi\App;
use Imi\Cli\CliApp;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class PHPUnitHook implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new class($this->executeBeforeFirstTest(...)) implements ExecutionStartedSubscriber {
            public function __construct(private \Closure $callback)
            {
            }

            public function notify(ExecutionStarted $event): void
            {
                ($this->callback)($event);
            }
        });
    }

    public function executeBeforeFirstTest(): void
    {
        App::run('Imi\ConnectionCenter\Test', CliApp::class, static function (): void {
        });
    }
}
