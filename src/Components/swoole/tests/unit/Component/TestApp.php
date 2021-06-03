<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component;

use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Swoole\SwooleApp;

class TestApp extends SwooleApp
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
