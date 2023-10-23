<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Model;

use Imi\Pgsql\Model\PgModel;
use Imi\Pgsql\Test\Model\Article;
use Imi\Pgsql\Test\Model\Member;
use Imi\Pgsql\Test\Model\MemberWithSqlField;
use Imi\Pgsql\Test\Model\NoIncPk;
use Imi\Pgsql\Test\Model\ReferenceGetterTestModel;
use Imi\Pgsql\Test\Model\TestJson;
use Imi\Pgsql\Test\Model\TestJsonNotCamel;
use Imi\Pgsql\Test\Model\TestSoftDelete;
use Imi\Pgsql\Test\Model\UpdateTime;
use Imi\Pgsql\Test\Model\VirtualColumn;
use Imi\Test\BaseTest;

/**
 * @testdox Model
 */
class ModelTest extends BaseTest
{
    public function testToArray(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $this->assertEquals([
            'id'        => null,
            'username'  => '1',
        ], $member->toArray());
    }

    public function testConvertToArray(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $this->assertEquals([
            'id'        => null,
            'username'  => '1',
        ], $member->convertToArray());

        $this->assertEquals([
            'id'        => null,
            'username'  => '1',
        ], $member->convertToArray(true));

        $this->assertEquals([
            'id'        => null,
            'username'  => '1',
            'password'  => '2',
            'notInJson' => null,
        ], $member->convertToArray(false));
    }

    public function testConvertListToArray(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $this->assertEquals([[
            'id'        => null,
            'username'  => '1',
        ]], Member::convertListToArray([$member]));

        $this->assertEquals([[
            'id'        => null,
            'username'  => '1',
        ]], Member::convertListToArray([$member], true));

        $this->assertEquals([[
            'id'        => null,
            'username'  => '1',
            'password'  => '2',
            'notInJson' => null,
        ]], Member::convertListToArray([$member], false));
    }

    public function testInsert(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $result = $member->insert();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());
        $id = $result->getLastInsertId();
        $this->assertEquals(1, $id);
        $this->assertEquals($id, $member->id);
    }

    public function testUpdate(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $result = $member->insert();
        $id = $result->getLastInsertId();
        $this->assertEquals(2, $id);

        $member->username = '3';
        $member->password = '4';
        $result = $member->update();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());

        $member = Member::find($id);
        $this->assertEquals([
            'id'        => $id,
            'username'  => '3',
            'password'  => '4',
            'notInJson' => null,
        ], $member->convertToArray(false));
    }

    public function testSave(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $result = $member->save();
        $id = $result->getLastInsertId();
        $this->assertEquals(1, $result->getAffectedRows());
        $this->assertEquals(3, $id);
        $this->assertEquals($id, $member->id);

        $member->username = '3';
        $member->password = '4';
        $result = $member->save();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());

        $member = Member::find($id);
        $this->assertEquals([
            'id'        => $id,
            'username'  => '3',
            'password'  => '4',
            'notInJson' => null,
        ], $member->convertToArray(false));

        $member->password = '5';
        $result = $member->save();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());

        $record = NoIncPk::newInstance();
        $record->aId = 1;
        $record->bId = 2;
        $record->value = 'imi';
        $record->save();

        $record2 = NoIncPk::find([
            'a_id' => 1,
            'b_id' => 2,
        ]);
        $this->assertNotNull($record2);
        $this->assertEquals([
            'aId'   => 1,
            'bId'   => 2,
            'value' => 'imi',
        ], $record2->toArray());

        $record2->value = 'yurun';
        $record2->save();

        $record3 = NoIncPk::find([
            'a_id' => 1,
            'b_id' => 2,
        ]);
        $this->assertNotNull($record3);
        $this->assertEquals([
            'aId'   => 1,
            'bId'   => 2,
            'value' => 'yurun',
        ], $record3->toArray());
    }

    public function testDelete(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $result = $member->insert();
        $id = $result->getLastInsertId();
        $this->assertGreaterThan(0, $id);

        $result = $member->delete();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());
    }

    public function testExists(): void
    {
        $this->assertTrue(Member::exists(1));
        $this->assertFalse(Member::exists(-1));
    }

    public function testFind(): void
    {
        $member = Member::find(1);
        $this->assertEquals([
            'id'        => 1,
            'username'  => '1',
            'password'  => '2',
            'notInJson' => null,
        ], $member->convertToArray(false));

        $member = Member::find([
            'id'    => 1,
        ]);
        $this->assertEquals([
            'id'        => 1,
            'username'  => '1',
            'password'  => '2',
            'notInJson' => null,
        ], $member->convertToArray(false));
    }

    public function testSelect(): void
    {
        $list = Member::select([
            'id'    => 1,
        ]);
        $this->assertEquals([
            [
                'id'        => '1',
                'username'  => '1',
            ],
        ], Member::convertListToArray($list));
        $this->assertEquals([
            [
                'id'        => '1',
                'username'  => '1',
            ],
        ], Member::convertListToArray($list, true));
        $this->assertEquals([
            [
                'id'        => '1',
                'username'  => '1',
                'password'  => '2',
                'notInJson' => null,
            ],
        ], Member::convertListToArray($list, false));
    }

    public function testDbQuery(): void
    {
        $list = Member::dbQuery()->field('id', 'username')->where('id', '=', 1)->select()->getArray();
        $this->assertEquals([
            [
                'id'        => 1,
                'username'  => '1',
            ],
        ], $list);
    }

    public function testDbQueryAlias(): void
    {
        $list = Member::dbQuery(null, null, 'a1')
            ->field('a1.id', 'username')
            ->where('a1.id', '=', 1)
            ->select()
            ->getArray();
        $this->assertEquals([
            [
                'id'        => 1,
                'username'  => '1',
            ],
        ], $list);
    }

    public function testQueryAlias(): void
    {
        /** @var Member $member */
        $member = Member::query(null, null, null, 'a1')
            ->field('a1.username')
            ->where('a1.id', '=', 1)
            ->select()
            ->get();
        $this->assertEquals([
            'username'  => '1',
        ], $member->toArray());
    }

    public function testQuerySetField(): void
    {
        /** @var Member $member */
        $member = Member::query()->field('username')->where('id', '=', 1)->select()->get();
        $this->assertEquals([
            'username'  => '1',
        ], $member->toArray());

        $member = Member::newInstance(['username' => 'test']);
        $member->password = 'password';
        $member->insert();
        $id = $member->id;
        $this->assertEquals([
            'id'        => $id,
            'username'  => 'test',
        ], $member->toArray());

        $member = Member::find($id);
        $this->assertEquals([
            'id'        => $id,
            'username'  => 'test',
        ], $member->toArray());
        $this->assertEquals('password', $member->password);
    }

    /**
     * @param UpdateTime $record
     */
    private static function assertAutoCreateOrUpdateTime($record, array $fields, float $startMicroTime): void
    {
        /** @phpstan-ignore-next-line */
        $parseDateTimeFun = (static fn (?string $columnType, $timeAccuracy, float $microTime) => PgModel::parseDateTime($columnType, $timeAccuracy, $microTime))->bindTo(null, PgModel::class);

        foreach ($fields as $field => $opts)
        {
            $value = $parseDateTimeFun($opts[0], $opts[1], $startMicroTime);
            self::assertEquals($value, $record->{$field}, sprintf('%s fail: %s', $field, $record->{$field}));
            if (isset($opts[2]))
            {
                self::assertStringMatchesFormat($opts[2], $value);
            }
        }
    }

    public function testAutoUpdateTime(): void
    {
        // 不支持增量更新的模型才能完成此测试
        self::assertFalse(UpdateTime::__getMeta()->isIncrUpdate());

        $fields = [
            'date'         => ['date', true, '%d-%d-%d'],
            'time'         => ['time', true, '%d:%d:%d.0'],
            'timetz'       => ['timetz', true, '%d:%d:%d.0'],
            'time2'        => ['time', 1000, '%d:%d:%d.%d'],
            'timetz2'      => ['timetz', 1000, '%d:%d:%d.%d'],
            'timestamp'    => ['timestamp', true, '%d-%d-%d %d:%d:%d.0'],
            'timestamptz'  => ['timestamptz', true, '%d-%d-%d %d:%d:%d.0'],
            'timestamp2'   => ['timestamp', 1000, '%d-%d-%d %d:%d:%d.%d'],
            'timestamptz2' => ['timestamptz', 1000, '%d-%d-%d %d:%d:%d.%d'],
            'int'          => ['int4', true, null],
            'bigint'       => ['int8', true, null],
        ];

        // create 测试
        $record = UpdateTime::newInstance();
        $result = $record->save();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000; // 为了避免浮点误差，这里加上 0.001
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);

        // update-1 测试
        $copyArr = $record->toArray();
        usleep(1000); // 延时 1 毫秒，避免时间相同
        $result = $record->update();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000; // 为了避免浮点误差，这里加上 0.001
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);
        // 更新后时间必须发生变化
        self::assertNotEquals($copyArr, $record->toArray());

        // update-2 测试
        $copyArr = $record->toArray();
        usleep(1000); // 延时 1 毫秒，避免时间相同
        $result = $record->save();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000; // 为了避免浮点误差，这里加上 0.001
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);
        // 更新后时间必须发生变化
        self::assertNotEquals($copyArr, $record->toArray());

        // 输入覆盖
        $record = UpdateTime::newInstance();
        $record->setDate('2000-01-01');
        $record->setTime('01:15:30');
        $record->setTimetz('01:15:30+08');
        $record->setTime2('01:15:30.45');
        $record->setTimetz2('01:15:30.45+08');
        $record->setTimestamp('2000-01-01 01:15:30');
        $record->setTimestamptz('2000-01-01 01:15:30+08');
        $record->setTimestamp2('2000-01-01 01:15:30.45');
        $record->setTimestamptz2('2000-01-01 01:15:30.45+08');
        $record->setInt(456);
        $record->setBigint(789);

        $fixed = $record->toArray();
        $record->save();
        $recordArr = $record->toArray();

        unset($fixed['id'], $recordArr['id']);

        self::assertEquals($fixed, $recordArr);
    }

    public function testModelReferenceGetter(): void
    {
        $model = ReferenceGetterTestModel::newInstance();
        $this->assertEquals([], $model->list);
        $model->list[] = 1;
        $this->assertEquals([1], $model->list);
        $model['list'][] = 2;
        $this->assertEquals([1, 2], $model['list']);
    }

    public function testJson(): void
    {
        $record = TestJson::newInstance();
        $record->jsonData = ['a' => 1, 'b' => 2, 'c' => 3];
        $record->insert();

        $record2 = TestJson::find($record->id);
        $this->assertNotNull($record2);
        $this->assertEquals($record->jsonData, $record2->jsonData->toArray());

        $record2->update([
            'json_data->a' => 111,
        ]);
        $record2 = TestJson::find($record->id);
        $this->assertNotNull($record2);
        $this->assertEquals(['a' => 111, 'b' => 2, 'c' => 3], $record2->jsonData->toArray());
    }

    public function testSoftDelete(): void
    {
        // 插入
        $record = TestSoftDelete::newInstance();
        $record->title = 'test';
        $result = $record->insert();
        $this->assertTrue($result->isSuccess());
        // 可以查到
        $this->assertNotNull(TestSoftDelete::find($record->id));

        // 软删除
        $result = $record->delete();
        $this->assertTrue($result->isSuccess());
        // 删除时间字段
        $this->assertNotEmpty($record->deleteTime);
        // 查不到
        $this->assertNull(TestSoftDelete::find($record->id));
        // 可以查到
        $this->assertNotNull(TestSoftDelete::findDeleted($record->id));

        // 恢复
        $record->restore();
        // 可以查到
        $this->assertNotNull(TestSoftDelete::find($record->id));

        // 物理删除
        $record->hardDelete();
        // 查不到
        $this->assertNull(TestSoftDelete::find($record->id));
        $this->assertNull(TestSoftDelete::findDeleted($record->id));
    }

    public function testSetFields(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $this->assertNull($member->__getSerializedFields());
        $this->assertEquals([
            'id'       => null,
            'username' => '1',
        ], $member->toArray());

        $member->__setSerializedFields(['username', 'password']);
        $this->assertEquals(['username', 'password'], $member->__getSerializedFields());
        $this->assertEquals([
            'username' => '1',
            'password' => '2',
        ], $member->toArray());
    }

    public function testSqlField(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $result = $member->insert();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());
        $id = $result->getLastInsertId();

        $record = MemberWithSqlField::find($id);
        $this->assertEquals([
            'id'       => $id,
            'username' => '1',
            'test1'    => 2,
            'test2'    => 4,
        ], $record->toArray());
    }

    public function testNotCamel(): void
    {
        $record = TestJson::newInstance([
            'jsonData' => '[1, 2, 3]',
        ]);
        $this->assertEquals([
            'id'       => null,
            'jsonData' => [1, 2, 3],
        ], $record->convertToArray());
        $this->assertEquals([1, 2, 3], $record->getJsonData()->toArray());
        $id = $record->insert()->getLastInsertId();
        $this->assertGreaterThan(0, $id);
        $record = TestJson::find($id);
        $this->assertEquals([
            'id'       => $id,
            'jsonData' => [1, 2, 3],
        ], $record->convertToArray());
        $this->assertEquals([1, 2, 3], $record->getJsonData()->toArray());
        $list = TestJson::query()->where('id', '=', $id)->select()->getArray();
        $this->assertEquals([[
            'id'       => $id,
            'jsonData' => [1, 2, 3],
        ]], TestJson::convertListToArray($list));

        $record = TestJsonNotCamel::newInstance([
            'json_data' => '[4, 5, 6]',
        ]);
        $this->assertEquals([
            'id'        => null,
            'json_data' => [4, 5, 6],
        ], $record->convertToArray());
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());
        $id = $record->insert()->getLastInsertId();
        $this->assertGreaterThan(0, $id);

        $record = TestJsonNotCamel::find($id);
        $this->assertEquals([
            'id'        => $id,
            'json_data' => [4, 5, 6],
        ], $record->convertToArray());
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());

        $list = TestJsonNotCamel::query()->where('id', '=', $id)->select()->getArray();
        $this->assertEquals([[
            'id'        => $id,
            'json_data' => [4, 5, 6],
        ]], TestJson::convertListToArray($list));

        $record = TestJsonNotCamel::query()->field('id', 'json_data')->where('id', '=', $id)->select()->get();
        $this->assertEquals([
            'id'        => $id,
            'json_data' => [4, 5, 6],
        ], $record->convertToArray());
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());
    }

    public function testModelConst(): void
    {
        $this->assertEquals('id', Article::PRIMARY_KEY);
        $this->assertEquals(['id'], Article::PRIMARY_KEYS);
    }

    public function testDbVirtualColumn(): void
    {
        $record1 = VirtualColumn::newInstance();
        $record1->amount = 123;
        $record1->insert();

        $record2 = VirtualColumn::find($record1->id);
        $this->assertEquals('1.23', $record2->virtualAmount);
    }
}
