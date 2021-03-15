<?php

declare(strict_types=1);

namespace Imi\Test\Component\Performance;

use Imi\App;
use Imi\Db\Db;
use Imi\Log\Log;
use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Performance;

/**
 * @testdox Performance:Model
 */
class ModelTest extends BaseTest
{
    public function testTruncate(): void
    {
        App::set('DB_LOG', false);
        $this->assertTrue(true);
        Db::getInstance()->exec('truncate tb_performance');
    }

    public function testInsert(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::newInstance([
                'value' => (string) ($i + 1),
            ])->insert();
        }
        Log::info('Model::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testUpdate(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
            $record->value = (string) (static::PERFORMANCE_COUNT - $i);
            $record->update();
        }
        Log::info('Model::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testFind(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record = Performance::find($i);
        }
        Log::info('Model::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testSelect(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        for ($i = 0; $i < static::PERFORMANCE_COUNT; ++$i)
        {
            Performance::query()->page(mt_rand(0, 99) * 100, 100);
        }
        Log::info('Model::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testToArray(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        $record = Performance::find(1);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record->toArray();
        }
        Log::info('Model::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }

    public function testConvertToArray(): void
    {
        $this->assertTrue(true);
        $time = microtime(true);
        $record = Performance::find(1);
        for ($i = 1; $i <= static::PERFORMANCE_COUNT; ++$i)
        {
            $record->convertToArray();
        }
        Log::info('Model::' . __FUNCTION__ . '(): ' . (microtime(true) - $time) . 's');
    }
}
