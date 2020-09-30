<?php
namespace Imi\Test\Component\Tests\Performance;

use Imi\Db\Db;
use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Performance;

abstract class BaseDbTest extends BaseTest
{
    public abstract function getPoolName(): string;

    public function testTruncate()
    {
        $this->assertTrue(true);
        Db::getInstance($this->getPoolName())->exec('truncate tb_performance');
    }

    public function testInsert()
    {
        $this->assertTrue(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('insert into tb_performance (`value`) values (?)');
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute([$i]);
        }
    }

    public function testUpdate()
    {
        $this->assertTrue(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('update tb_performance set `value` = ? where `id` = ?');
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute([$i + 1, $i + 1]);
        }
    }

    public function testSelect()
    {
        $this->assertTrue(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('select * from tb_performance limit 100');
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute();
        }
    }

    public function testFind()
    {
        $this->assertTrue(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('select * from tb_performance where `id` = ? limit 1');
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute([$i]);
        }
    }

}
