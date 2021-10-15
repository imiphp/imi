<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

trait TSwoolePgTest
{
    protected function setUp(): void
    {
        if (!\extension_loaded('swoole_postgresql'))
        {
            $this->markTestSkipped();
        }
    }
}
