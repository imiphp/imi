<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\TestRedisHashModel;
use Imi\Test\Component\Model\TestRedisHashWithFormatterModel;

/**
 * @testdox RedisModel Hash
 */
class RedisModelHashTest extends BaseTest
{
    public function testSave(): void
    {
        $record = TestRedisHashModel::newInstance([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $record->age = 11;
        $this->assertTrue($record->save());
    }

    public function testFind(): void
    {
        $expected = [
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ];
        $record = TestRedisHashModel::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNotNull($record);
        $this->assertEquals($expected, $record->toArray());
    }

    public function testSelect(): void
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
        $record = TestRedisHashModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $list = TestRedisHashModel::select([
            'id'    => 1,
            'name'  => 'a',
        ], [
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertEquals($expected, json_decode(json_encode($list), true));
    }

    public function testDelete(): void
    {
        $record = TestRedisHashModel::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNotNull($record);
        $this->assertTrue($record->delete());
        $this->assertNull(TestRedisHashModel::find([
            'id'    => 1,
            'name'  => 'a',
        ]));
    }

    public function testSafeDelete(): void
    {
        // --更新--
        // 原始记录
        $record = TestRedisHashModel::newInstance([
            'id'   => 114514,
            'name' => __METHOD__,
            'age'  => 22,
        ]);
        $this->assertTrue($record->save());

        // 查出2个对象实例
        $record1 = TestRedisHashModel::find([
            'name' => __METHOD__,
        ]);
        $this->assertNotNull($record1);
        $this->assertEquals($record->toArray(), $record1->toArray());
        $record2 = TestRedisHashModel::find([
            'name' => __METHOD__,
        ]);
        $this->assertNotNull($record2);
        $this->assertEquals($record->toArray(), $record2->toArray());

        // 更新一个
        $record1->age = 33;
        $record1->save();

        // 安全删除失败
        $this->assertFalse($record2->safeDelete());

        // 安全删除成功
        $this->assertTrue($record1->safeDelete());

        // --删除--
        // 原始记录
        $record = TestRedisHashModel::newInstance([
            'id'   => 114514,
            'name' => __METHOD__,
            'age'  => 22,
        ]);
        $this->assertTrue($record->save());

        // 查出2个对象实例
        $record1 = TestRedisHashModel::find([
            'name' => __METHOD__,
        ]);
        $this->assertNotNull($record1);
        $this->assertEquals($record->toArray(), $record1->toArray());
        $record2 = TestRedisHashModel::find([
            'name' => __METHOD__,
        ]);
        $this->assertNotNull($record2);
        $this->assertEquals($record->toArray(), $record2->toArray());

        // 更新一个
        $record1->delete();

        // 安全删除失败
        $this->assertFalse($record2->safeDelete());
    }

    public function testDeleteBatch(): void
    {
        $record = TestRedisHashModel::newInstance([
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ]);
        $this->assertTrue($record->save());
        $record = TestRedisHashModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $this->assertEquals(2, TestRedisHashModel::deleteBatch([
            'id'    => 1,
            'name'  => 'a',
        ], [
            'id'    => 2,
            'name'  => 'b',
        ]));
    }

    public function testFormatter(): void
    {
        $record = TestRedisHashWithFormatterModel::newInstance([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $record->age = 11;
        $this->assertTrue($record->save());

        $expected = [
            'id'    => 1,
            'name'  => 'a',
            'age'   => 11,
        ];
        $record = TestRedisHashWithFormatterModel::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNotNull($record);
        $this->assertEquals($expected, $record->toArray());

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
        $record = TestRedisHashWithFormatterModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $list = TestRedisHashWithFormatterModel::select([
            'id'    => 1,
            'name'  => 'a',
        ], [
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertEquals($expected, json_decode(json_encode($list), true));
    }
}
