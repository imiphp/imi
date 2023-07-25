<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\TestRedisModel;
use Imi\Test\Component\Model\TestRedisModel2;
use Imi\Test\Component\Model\TestRedisModelSerializable;
use Imi\Test\Component\Model\TestRedisWithFormatterModel;

/**
 * @testdox RedisModel
 */
class RedisModelTest extends BaseTest
{
    public function testSave(): void
    {
        $record = TestRedisModel::newInstance([
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

    public function testDelete(): void
    {
        $record = TestRedisModel::find('1-a');
        $this->assertNotNull($record);
        $this->assertTrue($record->delete());
        $this->assertNull(TestRedisModel::find('1-a'));
    }

    public function testSafeDelete(): void
    {
        // --更新--
        // 原始记录
        $record = TestRedisModel::newInstance([
            'id'    => 114514,
            'name'  => __METHOD__,
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());

        // 查出2个对象实例
        $record1 = TestRedisModel::find([
            'id'    => 114514,
            'name'  => __METHOD__,
        ]);
        $this->assertNotNull($record1);
        $this->assertEquals($record->toArray(), $record1->toArray());
        $record2 = TestRedisModel::find([
            'id'    => 114514,
            'name'  => __METHOD__,
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
        $record = TestRedisModel::newInstance([
            'id'    => 114514,
            'name'  => __METHOD__,
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());

        // 查出2个对象实例
        $record1 = TestRedisModel::find([
            'id'    => 114514,
            'name'  => __METHOD__,
        ]);
        $this->assertNotNull($record1);
        $this->assertEquals($record->toArray(), $record1->toArray());
        $record2 = TestRedisModel::find([
            'id'    => 114514,
            'name'  => __METHOD__,
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
     */
    public function testTTL(): void
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

        usleep(1500000);
        $record = TestRedisModel2::find([
            'id'    => 1,
            'name'  => 'a',
        ]);
        $this->assertNull($record);
    }

    public function testFormatter(): void
    {
        $record = TestRedisWithFormatterModel::newInstance([
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
        $record = TestRedisWithFormatterModel::find('formatter-1-a');
        $this->assertNotNull($record);
        $this->assertEquals($expected, $record->toArray());
        $record = TestRedisWithFormatterModel::find([
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
        $record = TestRedisWithFormatterModel::newInstance([
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ]);
        $this->assertTrue($record->save());
        $list = TestRedisWithFormatterModel::select('formatter-1-a', 'formatter-2-b');
        $this->assertEquals($expected, json_decode(json_encode($list), true));
        $list = TestRedisWithFormatterModel::select([
            'id'    => 1,
            'name'  => 'a',
        ], [
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertEquals($expected, json_decode(json_encode($list), true));
    }

    public function testSerialize(): void
    {
        $expected = [
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
        /** @var TestRedisModel $record */
        $record = unserialize(serialize($record));
        $this->assertTrue($record->save());
        $list = TestRedisModel::select('2-b');
        $this->assertEquals($expected, json_decode(json_encode($list), true));
        $list = TestRedisModel::select([
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertEquals($expected, json_decode(json_encode($list), true));

        $record = TestRedisModel::find([
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertNotNull($record);
        $this->assertEquals(22, $record->age);
        $record2 = unserialize(serialize($record));
        $this->assertEquals($record->toArray(), $record2->toArray());
    }

    public function testSerializable(): void
    {
        $data = [
            'id'    => 2,
            'name'  => 'b',
            'age'   => 22,
        ];
        $record = TestRedisModelSerializable::newInstance($data);
        $record->save();

        $record2 = TestRedisModelSerializable::find([
            'id'    => 2,
            'name'  => 'b',
        ]);
        $this->assertNotNull($record2);
        foreach ($data as $name => $value)
        {
            $this->assertEquals($value, $record2->{$name});
        }
    }
}
