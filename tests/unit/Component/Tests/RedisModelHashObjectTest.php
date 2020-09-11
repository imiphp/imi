<?php

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\TestRedisHashObjectModel;

/**
 * @testdox RedisModel HashObject
 */
class RedisModelHashObjectTest extends BaseTest
{
    public function testSave()
    {
        $record = TestRedisHashObjectModel::newInstance([
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
        $record = TestRedisHashObjectModel::find([
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
        $record = TestRedisHashObjectModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $list = TestRedisHashObjectModel::select([
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
        $record = TestRedisHashObjectModel::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNotNull($record);
        $this->assertTrue($record->delete());
    }

    public function testDeleteBatch()
    {
        $record = TestRedisHashObjectModel::newInstance([
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ]);
        $this->assertTrue($record->save());
        $record = TestRedisHashObjectModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $this->assertEquals(2, TestRedisHashObjectModel::deleteBatch([
            'id'    => 1,
            'name'  => 'a',
        ], [
            'id'    => 2,
            'name'  => 'b',
        ]));
    }
}
