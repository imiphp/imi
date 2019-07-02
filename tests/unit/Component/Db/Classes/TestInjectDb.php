<?php
namespace Imi\Test\Component\Db\Classes;

use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;
use Imi\Db\Annotation\DbInject;
use Imi\Db\Interfaces\IDb;

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

    public function test()
    {
        Assert::assertInstanceOf(IDb::class, $this->db);
    }
}