<?php

declare(strict_types=1);

namespace Imi\Test\Component;

use Imi\Cli\CliApp;
use Imi\Core\App\Enum\LoadRuntimeResult;

class TestApp extends CliApp
{
    /**
     * 运行应用.
     */
    public function run(): void
    {
    }

    /**
     * 加载运行时.
     */
    public function loadRuntime(): int
    {
        return LoadRuntimeResult::NONE;
    }
}
