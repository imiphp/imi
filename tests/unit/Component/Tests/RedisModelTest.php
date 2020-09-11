<?php

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\TestRedisModel;
use Imi\Test\Component\Model\TestRedisModel2;

/**
 * @testdox RedisModel
 */
class RedisModelTest extends BaseTest
{
    public function testSave()
    {
        $record = TestRedisModel::newInstance([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $record->age = 11;
        $this->assertTrue($record->save());
    }

    public function testFind()
    {
        $expected = [
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ];
        $record = TestRedisModel::find('1-a');
        $this->assertNotNull($record);
        $this->assertEquals($expected, $record->toArray());
        $record = TestRedisModel::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNotNull($record);
        $this->assertEquals($expected, $record->toArray());
    }

    public function testSelect()
    {
        $expected = [
            [
                'id'    => 1,
                'name'  => 'a',
                'age'   => 11,
            ],
            [
                'id'    => 2,
                'name'  => 'b',
                'age'   => 22,
            ],
        ];
        $record = TestRedisModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $list = TestRedisModel::select('1-a', '2-b');
        $this->assertEquals($expected, json_decode(json_encode($list), true));
        $list = TestRedisModel::select([
            'id'    => 1,
            'name'  => 'a',
        ], [
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertEquals($expected, json_decode(json_encode($list), true));
    }

    public function testDelete()
    {
        $record = TestRedisModel::find('1-a');
        $this->assertNotNull($record);
        $this->assertTrue($record->delete());
    }

    public function testDeleteBatch()
    {
        $record = TestRedisModel::newInstance([
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ]);
        $this->assertTrue($record->save());
        $record = TestRedisModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $this->assertEquals(2, TestRedisModel::deleteBatch([
            'id'    => 1,
            'name'  => 'a',
        ], '2-b'));
    }

    /**
     * @testdox ttl
     *
     * @return void
     */
    public function testTTL()
    {
        $expected = [
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ];
        $record = TestRedisModel2::newInstance($expected);
        $this->assertTrue($record->save());

        $record = TestRedisModel2::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertEquals($expected, $record->toArray());

        sleep(3);
        $record = TestRedisModel2::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNull($record);
    }
}
