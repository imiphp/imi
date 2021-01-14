<?php

declare(strict_types=1);

namespace Imi\Test\Component;

use Imi\App;
use Imi\Cli\CliApp;
use Imi\Core\App\Enum\LoadRuntimeResult;

class TestApp extends CliApp
{
    /**
     * 运行应用.
     *
     * @return void
     */
    public function run(): void
    {
        register_shutdown_function(function () {
            App::getBean('Logger')->save();
        });
    }

    /**
     * 加载运行时.
     *
     * @return int
     */
    public function loadRuntime(): int
    {
        return LoadRuntimeResult::NONE;
    }
}
