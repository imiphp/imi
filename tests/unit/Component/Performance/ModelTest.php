<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Performance;

use Imi\Db\Db;
use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Performance;

/**
 * @testdox Performance:Model
 */
class ModelTest extends BaseTest
{
    public function testTruncate()
    {
        $this->assertTrue(true);
        Db::getInstance()->exec('truncate tb_performance');
    }

    public function testInsert()
    {
        $this->assertTrue(true);
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::newInstance([
                'value' => $i + 1,
            ])->insert();
        }
    }

    public function testUpdate()
    {
        $this->assertTrue(true);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
            $record->value = static::PERFORMANCE_COUNT - $i;
            $record->update();
        }
    }

    public function testFind()
    {
        $this->assertTrue(true);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
        }
    }

    public function testSelect()
    {
        $this->assertTrue(true);
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::query()->limit(mt_rand(0, 99) * 100, 100);
        }
    }

    public function testToArray()
    {
        $this->assertTrue(true);
        $record = Performance::find(1);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record->toArray();
        }
    }

    public function testConvertToArray()
    {
        $this->assertTrue(true);
        $record = Performance::find(1);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record->convertToArray();
        }
    }
}
