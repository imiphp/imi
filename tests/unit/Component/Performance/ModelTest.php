<?php
namespace Imi\Test\Component\Tests\Performance;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Performance;

/**
 * @testdox Performance:Model
 */
class ModelTest extends BaseTest
{
    public function testInsert()
    {
        $this->assertTrue(true);
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::newInstance([
                'value' =>  $i + 1,
            ])->insert();
        }
    }

    public function testSelect()
    {
        $this->assertTrue(true);
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::query()->limit(mt_rand(0, 99) * 100, 100);
        }
    }

    public function testFind()
    {
        $this->assertTrue(true);
        for($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
        }
    }

    public function testUpdate()
    {
        $this->assertTrue(true);
        for($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
            $record->value = static::PERFORMANCE_COUNT - $i;
            $record->update();
        }
    }

    public function testToArray()
    {
        $this->assertTrue(true);
        $record = Performance::find(1);
        for($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record->toArray();
        }
    }

    public function testConvertToArray()
    {
        $this->assertTrue(true);
        $record = Performance::find(1);
        for($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record->convertToArray();
        }
    }

}
