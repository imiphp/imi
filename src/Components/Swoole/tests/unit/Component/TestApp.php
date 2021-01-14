<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component;

use Imi\App;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Swoole\SwooleApp;

class TestApp extends SwooleApp
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
