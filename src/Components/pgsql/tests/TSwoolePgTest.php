<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

trait TSwoolePgTest
{
    protected function setUp(): void
    {
        if (!class_exists(\Swoole\Coroutine\PostgreSQL::class, false) || version_compare(\SWOOLE_VERSION, '5.1.1', '<='))
        {
            $this->markTestSkipped();
        }
    }
}
