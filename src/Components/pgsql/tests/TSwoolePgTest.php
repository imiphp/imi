<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

trait TSwoolePgTest
{
    protected function setUp(): void
    {
        if (!class_exists(\Swoole\Coroutine\PostgreSQL::class, false))
        {
            $this->markTestSkipped();
        }
    }
}
