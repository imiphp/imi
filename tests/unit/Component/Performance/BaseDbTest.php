<?php

declare(strict_types=1);

namespace Imi\Test\Component\Performance;

use Imi\App;
use Imi\Db\Db;
use Imi\Log\Log;
use Imi\Test\BaseTest;

abstract class BaseDbTest extends BaseTest
{
    abstract public function getPoolName(): string;

    public function testTruncate(): void
    {
        App::set('DB_LOG', false);
        $this->assertTrue(true);
        Db::getInstance($this->getPoolName())->exec('truncate tb_performance');
    }

    public function testInsert(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('insert into tb_performance (`value`) values (?)');
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute([$i]);
        }
        Log::info($this->getPoolName() . '::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testUpdate(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('update tb_performance set `value` = ? where `id` = ?');
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute([$i + 1, $i + 1]);
        }
        Log::info($this->getPoolName() . '::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testSelect(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('select * from tb_performance limit 100');
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute();
        }
        Log::info($this->getPoolName() . '::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testFind(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        $stmt = Db::getInstance($this->getPoolName())->prepare('select * from tb_performance where `id` = ? limit 1');
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            $stmt->execute([$i]);
        }
        Log::info($this->getPoolName() . '::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }
}
