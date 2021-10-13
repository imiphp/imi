<?php

declare(strict_types=1);

namespace Imi\AMQP\Test;

use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Swoole\SwooleApp;

class TestApp extends SwooleApp
{
    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function loadRuntime(): int
    {
        return LoadRuntimeResult::NONE;
    }
}
