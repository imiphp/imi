<?php
namespace Imi\Test\Component\Tests\Performance;

use Imi\Log\Log;
use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Performance;

/**
 * @testdox Performance:Model
 */
class ModelTest extends BaseTest
{
    public function startTest()
    {
        Log::log('Test', 'Performance:Model');
    }

    public function testInsert()
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::newInstance([
                'value' =>  $i + 1,
            ])->insert();
        }
        Log::log('Test', sprintf('Model->insert(): %s s', microtime(true) - $time));
    }

    public function testSelect()
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::query()->limit(mt_rand(0, 99) * 100, 100);
        }
        Log::log('Test', sprintf('Model->select(): %s s', microtime(true) - $time));
    }

    public function testFind()
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
        }
        Log::log('Test', sprintf('Model->find(): %s s', microtime(true) - $time));
    }

    public function testUpdate()
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
            $record->value = static::PERFORMANCE_COUNT - $i;
            $record->update();
        }
        Log::log('Test', sprintf('Model->update(): %s s', microtime(true) - $time));
    }


}
