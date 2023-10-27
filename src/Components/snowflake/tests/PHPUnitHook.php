<?php

declare(strict_types=1);

namespace Imi\Snowflake\Test;

use Imi\App;
use Imi\Cli\CliApp;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class PHPUnitHook implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        App::run('Imi\Snowflake\Test', CliApp::class, static function (): void {
        });
    }
}
