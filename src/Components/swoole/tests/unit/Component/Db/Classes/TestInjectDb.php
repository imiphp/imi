<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Db\Classes;

use Imi\Bean\Annotation\Bean;
use Imi\Db\Annotation\DbInject;
use Imi\Db\Interfaces\IDb;
use PHPUnit\Framework\Assert;

#[Bean(name: 'TestInjectDb')]
class TestInjectDb
{
    #[DbInject]
    protected IDb $db;

    public function test(): void
    {
        Assert::assertInstanceOf(IDb::class, $this->db);
    }
}
