<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

trait TPdoPgTest
{
    protected function setUp(): void
    {
        if (!\in_array('pgsql', pdo_drivers()))
        {
            $this->markTestSkipped();
        }
    }
}
