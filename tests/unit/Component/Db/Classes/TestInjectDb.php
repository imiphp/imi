<?php

namespace Imi\Test\Component\Db\Classes;

use Imi\Bean\Annotation\Bean;
use Imi\Db\Annotation\DbInject;
use Imi\Db\Interfaces\IDb;
use PHPUnit\Framework\Assert;

/**
 * @Bean("TestInjectDb")
 */
class TestInjectDb
{
    /**
     * @DbInject
     *
     * @var \Imi\Db\Interfaces\IDb
     */
    protected $db;

    /**
     * @return void
     */
    public function test()
    {
        Assert::assertInstanceOf(IDb::class, $this->db);
    }
}
