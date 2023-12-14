<?php

declare(strict_types=1);

namespace Imi\Model\Test\Tests;

use Imi\Db\Db;
use Imi\Model\Annotation\DDL;
use Imi\Model\Model;
use Imi\Model\Test\Model\Article;
use Imi\Model\Test\Model\Article2;
use Imi\Model\Test\Model\ArticleEx;
use Imi\Model\Test\Model\ArticleId;
use Imi\Model\Test\Model\CreateTime;
use Imi\Model\Test\Model\Member;
use Imi\Model\Test\Model\MemberReferenceProperty;
use Imi\Model\Test\Model\MemberSerializable;
use Imi\Model\Test\Model\MemberWithSqlField;
use Imi\Model\Test\Model\NoIncPk;
use Imi\Model\Test\Model\Prefix;
use Imi\Model\Test\Model\ReferenceGetterTestModel;
use Imi\Model\Test\Model\TestBug403;
use Imi\Model\Test\Model\TestEnum;
use Imi\Model\Test\Model\TestFieldName;
use Imi\Model\Test\Model\TestFieldNameNotCamel;
use Imi\Model\Test\Model\TestJson;
use Imi\Model\Test\Model\TestJsonEncodeDecode2;
use Imi\Model\Test\Model\TestJsonEncodeDecodeArraywarp;
use Imi\Model\Test\Model\TestJsonEncodeDecodeCallable;
use Imi\Model\Test\Model\TestJsonEncodeDecodeNone;
use Imi\Model\Test\Model\TestJsonExtractProperty;
use Imi\Model\Test\Model\TestJsonNotCamel;
use Imi\Model\Test\Model\TestList;
use Imi\Model\Test\Model\TestSet;
use Imi\Model\Test\Model\TestSoftDelete;
use Imi\Model\Test\Model\TestWithMember;
use Imi\Model\Test\Model\UpdateTime;
use Imi\Model\Test\Model\VirtualColumn;
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
            'id'       => null,
            'username' => '1',
        ], $member->toArray());
    }

    public function testConvertToArray(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $this->assertEquals([
            'id'       => null,
            'username' => '1',
        ], $member->convertToArray());

        $this->assertEquals([
            'id'       => null,
            'username' => '1',
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
            'id'       => null,
            'username' => '1',
        ]], Member::convertListToArray([$member]));

        $this->assertEquals([[
            'id'       => null,
            'username' => '1',
        ]], Member::convertListToArray([$member], true));

        $this->assertEquals([[
            'id'        => null,
            'username'  => '1',
            'password'  => '2',
            'notInJson' => null,
        ]], Member::convertListToArray([$member], false));
    }

    public function testInsert(): array
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->__setRaw('password', "CONCAT('p', 'w2')");
        $result = $member->insert();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());
        $id = $result->getLastInsertId();
        $this->assertGreaterThan(0, $id);
        $this->assertEquals($id, $member->id);

        return [
            'id' => $member->id,
        ];
    }

    public function testUpdate(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->__setRaw('password', "CONCAT('p', 'w2')");
        $result = $member->insert();
        $id = $result->getLastInsertId();
        $this->assertGreaterThan(0, $id);

        $member->username = '3';
        $member->__setRaw('password', "CONCAT('p', 'w4')");
        $result = $member->update();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());

        $member = Member::find($id);
        $this->assertEquals([
            'id'        => $id,
            'username'  => '3',
            'password'  => 'pw4',
            'notInJson' => null,
        ], $member->convertToArray(false));
    }

    public function testSave(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->__setRaw('password', "CONCAT('p', 'w2')");
        $result = $member->save();
        $id = $result->getLastInsertId();
        $this->assertEquals(1, $result->getAffectedRows());
        $this->assertGreaterThan(0, $id);
        $this->assertEquals($id, $member->id);

        $member->username = '3';
        $member->__setRaw('password', "CONCAT('p', 'w4')");
        $result = $member->save();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());

        $member = Member::find($id);
        $this->assertEquals([
            'id'        => $id,
            'username'  => '3',
            'password'  => 'pw4',
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

        $record4 = NoIncPk::newInstance();
        $record4->setAId(1);
        $record4->setBId(2);
        $record4->setValue('yurun2');
        $record4->save();
        $record5 = NoIncPk::find([
            'a_id' => 1,
            'b_id' => 2,
        ]);
        $this->assertNotNull($record5);
        $this->assertEquals([
            'aId'   => 1,
            'bId'   => 2,
            'value' => 'yurun2',
        ], $record5->toArray());
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

    /**
     * @depends testInsert
     */
    public function testExists(array $args): void
    {
        ['id' => $id] = $args;
        $this->assertTrue(Member::exists($id));
        $this->assertFalse(Member::exists(-1));
    }

    /**
     * @depends testInsert
     */
    public function testFind(array $args): void
    {
        ['id' => $id] = $args;
        $member = Member::find($id);
        $this->assertEquals([
            'id'        => $id,
            'username'  => '1',
            'password'  => 'pw2',
            'notInJson' => null,
        ], $member->convertToArray(false));

        $member = Member::find([
            'id' => $id,
        ]);
        $this->assertEquals([
            'id'        => $id,
            'username'  => '1',
            'password'  => 'pw2',
            'notInJson' => null,
        ], $member->convertToArray(false));
    }

    /**
     * @depends testInsert
     */
    public function testSelect(array $args): void
    {
        ['id' => $id] = $args;
        $list = Member::select([
            'id' => $id,
        ]);
        $this->assertEquals([
            [
                'id'       => $id,
                'username' => '1',
            ],
        ], Member::convertListToArray($list));
        $this->assertEquals([
            [
                'id'       => $id,
                'username' => '1',
            ],
        ], Member::convertListToArray($list, true));
        $this->assertEquals([
            [
                'id'        => $id,
                'username'  => '1',
                'password'  => 'pw2',
                'notInJson' => null,
            ],
        ], Member::convertListToArray($list, false));
    }

    /**
     * @depends testInsert
     */
    public function testDbQuery(array $args): void
    {
        ['id' => $id] = $args;
        $list = Member::dbQuery()->field('id', 'username')->where('id', '=', $id)->select()->getArray();
        $this->assertEquals([
            [
                'id'       => $id,
                'username' => '1',
            ],
        ], $list);
    }

    /**
     * @depends testInsert
     */
    public function testDbQueryAlias(array $args): void
    {
        ['id' => $id] = $args;
        $list = Member::dbQuery(null, null, 'a1')
            ->field('a1.id', 'a1.username')->where('a1.id', '=', $id)
            ->select()
            ->getArray();
        $this->assertEquals([
            [
                'id'       => $id,
                'username' => '1',
            ],
        ], $list);
    }

    /**
     * @depends testInsert
     */
    public function testQueryFind(array $args): void
    {
        ['id' => $id] = $args;
        $result = Member::query()
            ->where('id', '=', $id)
            ->find();
        $this->assertInstanceOf(Member::class, $result);
        $this->assertEquals([
            'id'       => $id,
            'username' => '1',
        ], $result->toArray());
    }

    /**
     * @depends testInsert
     */
    public function testQueryAlias(array $args): void
    {
        ['id' => $id] = $args;
        $result = Member::query(null, null, null, 'a1')
            ->where('a1.id', '=', $id)
            ->find();
        $this->assertInstanceOf(Member::class, $result);
        $this->assertEquals([
            'id'       => $id,
            'username' => '1',
        ], $result->toArray());
    }

    /**
     * @depends testInsert
     */
    public function testValue(array $args): void
    {
        ['id' => $id] = $args;

        $value = Member::query()
            ->where('id', '=', $id)
            ->value('username');
        $this->assertEquals('1', $value);

        $value = Member::query()
            ->where('id', '=', -1)
            ->value('id', '9999999');
        $this->assertEquals('9999999', $value);
    }

    /**
     * @depends testBatchInsert
     */
    public function testColumn(array $args): void
    {
        $origin = $args['origin'];

        $data = Member::query()
            ->column('username');

        $this->assertEquals(array_column($origin, 'username'), $data);

        $data = Member::query()
            ->column('username', 'id');

        $this->assertEquals(array_column($origin, 'username', 'id'), $data);

        $data = Member::query()
            ->column(['id', 'username'], 'id');

        $this->assertEquals(array_column_ex($origin, ['id', 'username'], 'id'), $data);

        $data = Member::query()
            ->column(['username', 'id'], 'id');

        $this->assertEquals(array_column_ex($origin, ['username', 'id'], 'id'), $data);
    }

    private static function assertAutoCreateOrUpdateTime(CreateTime|UpdateTime $record, array $fields, float $startMicroTime): void
    {
        /** @phpstan-ignore-next-line */
        $parseDateTimeFun = (static fn (?string $columnType, $timeAccuracy, float $microTime) => Model::parseDateTime($columnType, $timeAccuracy, $microTime))->bindTo(null, Model::class);

        foreach ($fields as $field => $opts)
        {
            self::assertEquals($parseDateTimeFun($opts[0], $opts[1], $startMicroTime), $record->{$field}, sprintf('%s fail: %s', $field, $record->{$field}));
        }
    }

    public function testAutoCreateTime(): void
    {
        // 不支持增量更新的模型才能完成此测试
        self::assertFalse(CreateTime::__getMeta()->isIncrUpdate());

        $fields = [
            'date'         => ['date', true],
            'time'         => ['time', true],
            'datetime'     => ['datetime', true],
            'timestamp'    => ['timestamp', true],
            'int'          => ['int', true],
            'bigint'       => ['bigint', true],
            'year'         => ['year', true],
            'bigintSecond' => ['bigint', 1],
        ];

        // save 测试
        $record = CreateTime::newInstance();
        $result = $record->save();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000;
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);

        // 更新测试
        $fixed = $record->toArray();
        $record->update();
        $this->assertEquals($fixed, $record->toArray());
        $record->save();
        $this->assertEquals($fixed, $record->toArray());

        // insert 测试
        $record = CreateTime::newInstance();
        $record->insert();
        $startMicroTime = ($record->getBigint() + 0.001) / 1000;
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);

        // 输入覆盖
        $record = CreateTime::newInstance();
        $record->setDate('2000-01-01');
        $record->setTime('01:15:30');
        $record->setDatetime('2000-01-01 01:15:30');
        $record->setTimestamp('2000-01-02 01:15:30');
        $record->setInt(1);
        $record->setBigint(2);
        $record->setYear(3);
        $record->setBigintSecond(4);

        $fixed = $record->toArray();
        $record->save();
        $recordArr = $record->toArray();

        unset($fixed['id'], $recordArr['id']);

        self::assertEquals($fixed, $recordArr);
    }

    public function testAutoUpdateTime(): void
    {
        // 不支持增量更新的模型才能完成此测试
        self::assertFalse(UpdateTime::__getMeta()->isIncrUpdate());

        $fields = [
            'date'         => ['date', true],
            'time'         => ['time', true],
            'datetime'     => ['datetime', true],
            'timestamp'    => ['timestamp', true],
            'int'          => ['int', true],
            'bigint'       => ['bigint', true],
            'year'         => ['year', true],
            'bigintSecond' => ['bigint', 1],
        ];

        // create 测试
        $record = UpdateTime::newInstance();
        $result = $record->save();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000;
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);

        // update-1 测试
        $copyArr = $record->toArray();
        usleep(1000); // 延时 1 毫秒，避免时间相同
        $result = $record->update();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000;
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);
        // 更新后时间必须发生变化
        self::assertNotEquals($copyArr, $record->toArray());

        // update-2 测试
        $copyArr = $record->toArray();
        usleep(1000); // 延时 1 毫秒，避免时间相同
        $result = $record->save();
        self::assertTrue($result->isSuccess());
        $startMicroTime = ($record->getBigint() + 0.001) / 1000;
        self::assertAutoCreateOrUpdateTime($record, $fields, $startMicroTime);
        // 更新后时间必须发生变化
        self::assertNotEquals($copyArr, $record->toArray());

        // 输入覆盖
        $record = UpdateTime::newInstance();
        $record->setDate('2000-01-01');
        $record->setTime('01:15:30');
        $record->setDatetime('2000-01-01 01:15:30');
        $record->setTimestamp('2000-01-02 01:15:30');
        $record->setInt(1);
        $record->setBigint(2);
        $record->setYear(3);
        $record->setBigintSecond(4);

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
        // @phpstan-ignore-next-line
        $this->assertEquals($record->jsonData, $record2->jsonData->toArray());
    }

    public function testList(): void
    {
        $record = TestList::newInstance();
        $record->list = [1, 2, 3];
        $record->insert();

        $record2 = TestList::find($record->id);
        $this->assertNotNull($record2);
        $this->assertEquals($record->list, $record2->list);
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

        // join
        /** @var TestSoftDelete $record2 */
        $record2 = TestSoftDelete::query()->join('tb_test_soft_delete as b', 'b.id', '=', 'tb_test_soft_delete.id')->where('tb_test_soft_delete.id', '=', $record->id)->select()->get();
        $this->assertEquals($record->toArray(), $record2->toArray());
        /** @var TestSoftDelete $record2 */
        $record2 = TestSoftDelete::query()->table('tb_test_soft_delete', 'a')->join('tb_test_soft_delete as b', 'b.id', '=', 'a.id')->where('a.id', '=', $record->id)->select()->get();
        $this->assertEquals($record->toArray(), $record2->toArray());

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
        // @phpstan-ignore-next-line
        $this->assertEquals([1, 2, 3], $record->getJsonData()->toArray());
        $id = $record->insert()->getLastInsertId();
        $this->assertGreaterThan(0, $id);
        $record = TestJson::find($id);
        $this->assertEquals([
            'id'       => $id,
            'jsonData' => [1, 2, 3],
        ], $record->convertToArray());
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());
        $id = $record->insert()->getLastInsertId();
        $this->assertGreaterThan(0, $id);

        $record = TestJsonNotCamel::find($id);
        $this->assertEquals([
            'id'        => $id,
            'json_data' => [4, 5, 6],
        ], $record->convertToArray());
        // @phpstan-ignore-next-line
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());

        $list = TestJsonNotCamel::query()->where('id', '=', $id)->select()->getArray();
        $this->assertEquals([[
            'id'        => $id,
            'json_data' => [4, 5, 6],
        ]], TestJson::convertListToArray($list));

        $record = TestJsonNotCamel::query()->where('id', '=', $id)->select()->get();
        $this->assertEquals([
            'id'        => $id,
            'json_data' => [4, 5, 6],
        ], $record->convertToArray());
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());
    }

    public function testFork(): void
    {
        $article2 = Article2::fork('tb_article', 'mysqli');
        $this->assertEquals($article2, Article2::fork('tb_article', 'mysqli'));

        /** @var Article2 $record */
        $record = $article2::newInstance();
        $record->memberId = 1024;
        $record->title = __CLASS__;
        $record->content = __FUNCTION__;
        $record->save();
        $this->assertGreaterThan(0, $record->id);

        /** @var Article2 $record */
        $record = $article2::find($record->id);
        $this->assertNotNull($record);

        $result = Db::query()->from('tb_article')->where('id', '=', $record->id)->select()->get();
        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
        foreach ($result as $k => $v)
        {
            $this->assertEquals($record[$k], $v);
        }
    }

    public function testNotBean(): void
    {
        $record = Article2::newInstance();
        $this->assertEquals(Article2::class, $record::class);
        $record->memberId = 1024;
        $record->title = __CLASS__;
        $record->content = __FUNCTION__;
        $record->save();
        $this->assertGreaterThan(0, $record->id);

        $record = Article2::find($record->id);
        $this->assertNotNull($record);
        $this->assertEquals(Article2::class, $record::class);
    }

    public function testReferenceProperty(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $member->insert();

        $record = MemberReferenceProperty::find($member->id);
        $this->assertEquals($member->id, $record->id);
        $this->assertEquals($member->id, $record->id2);
    }

    public function testCustomFields(): void
    {
        $member = Member::newInstance();
        $member->username = '1';
        $member->password = '2';
        $member->insert();

        $memberArray = $member->toArray();
        $memberArray['id2'] = $memberArray['id'];

        /** @var MemberReferenceProperty|null $member1 */
        $member1 = MemberReferenceProperty::query()->where('id', '=', $member->id)->select()->get();
        $this->assertNotNull($member1);
        $this->assertEquals($memberArray, $member1->toArray());

        /** @var MemberReferenceProperty|null $member1 */
        $list = MemberReferenceProperty::query()->where('id', '=', $member->id)->select()->getArray();
        $this->assertNotNull($member1);
        $this->assertEquals([$memberArray], Member::convertListToArray($list));
    }

    public function testBatchInsert(): array
    {
        $basicRowCount = Member::count();

        $insertCount = 100;
        $data = [];

        for ($i = 1; $i <= $insertCount; ++$i)
        {
            $data[] = [
                'username' => "username_{$i}",
                'password' => "password_{$i}",
            ];
        }
        Member::dbQuery()->batchInsert($data);

        $newRowCount = Member::count();

        $this->assertEquals($basicRowCount + $insertCount, $newRowCount);

        $items = [];
        foreach (Member::select() as $item)
        {
            $items[] = $item->toArray();
        }

        return [
            'origin' => $items,
        ];
    }

    /**
     * @depends testBatchInsert
     */
    public function testCursor(array $args): void
    {
        $data = [];
        foreach (Member::query()->cursor() as $item)
        {
            $data[] = $item->toArray();
        }

        $this->assertEquals($args['origin'], $data);
    }

    /**
     * @depends testBatchInsert
     */
    public function testChunkById(array $args): void
    {
        $data = [];
        foreach (Member::query()->chunkById(32, 'id') as $items)
        {
            foreach ($items->getArray() as $item)
            {
                $data[] = $item->toArray();
            }
        }

        $this->assertEquals($args['origin'], $data);
    }

    /**
     * @depends testBatchInsert
     */
    public function testChunkByOffset(array $args): void
    {
        $data = [];
        foreach (Member::query()->chunkByOffset(32) as $items)
        {
            foreach ($items->getArray() as $item)
            {
                $data[] = $item->toArray();
            }
        }

        $this->assertEquals($args['origin'], $data);
    }

    /**
     * @depends testBatchInsert
     */
    public function testChunkEach(array $args): void
    {
        $data = [];
        foreach (Member::query()->chunkById(32, 'id')->each() as $item)
        {
            $data[] = $item->toArray();
        }

        $this->assertEquals($args['origin'], $data);
    }

    public function testJsonNullValue(): void
    {
        if (ArticleEx::exists(199))
        {
            ArticleEx::dbQuery()->where('article_id', '=', 199)->delete();
        }

        $model = ArticleEx::newInstance();
        $model->articleId = 199;
        $model->data = new \stdClass();
        $model->save();

        $jsonValue = ArticleEx::dbQuery()
            ->where('article_id', '=', 199)
            ->value('data');
        $this->assertEquals('{}', $jsonValue);

        $model = ArticleEx::find(199);
        $model->data = null;
        $model->save();

        $jsonValue = ArticleEx::dbQuery()
            ->where('article_id', '=', 199)
            ->value('data');
        $this->assertNull($jsonValue);

        $model->delete();
    }

    public function testEnum(): void
    {
        $record = TestEnum::newInstance();
        $record->insert();
        $this->assertEquals([
            'id'     => 1,
            'value1' => '\'test\'',
            'value2' => '1',
        ], $record->toArray());

        $record = TestEnum::newInstance();
        $record->value1 = 'b';
        $record->value2 = '2';
        $record->insert();
        $this->assertEquals([
            'id'     => 2,
            'value1' => 'b',
            'value2' => '2',
        ], $record->toArray());
    }

    public function testSet(): void
    {
        $record = TestSet::newInstance();
        $record->insert();
        $this->assertEquals([
            'id'     => 1,
            'value1' => ['\'test\''],
            'value2' => ['1', '2'],
        ], $record->toArray());

        $record = TestSet::newInstance();
        $record->value1 = ['a', 'b'];
        $record->value2 = ['2', '3'];
        $record->insert();
        $this->assertEquals([
            'id'     => 2,
            'value1' => ['a', 'b'],
            'value2' => ['2', '3'],
        ], $record->toArray());
    }

    /**
     * @see https://github.com/imiphp/imi/issues/355
     */
    public function testBug355(): void
    {
        $member = MemberSerializable::newInstance();
        $member->username = 'testBug355_username';
        $member->password = 'testBug355_password';
        $member->insert();

        $record1 = TestWithMember::newInstance();
        $record1->memberId = $member->id;
        $record1->insert();

        $record2 = TestWithMember::query()->with(['member'])->where('id', '=', $record1->id)->select()->get();
        $this->assertNotNull($record2);
        $this->assertNotNull($record2->member);
        $data = $record2->toArray();
        $this->assertFalse(isset($data['memberId']));

        $record2 = TestWithMember::query()->with(['member'])->where('id', '=', $record1->id)->select()->getArray()[0] ?? null;
        $this->assertNotNull($record2);
        $this->assertNotNull($record2->member);
        $data = $record2->toArray();
        $this->assertFalse(isset($data['memberId']));
    }

    public function testPrefix(): void
    {
        $record1 = Prefix::newInstance();
        $record1->name = 'imi';
        $record1->insert();
        $this->assertGreaterThan(0, $record1->id);

        $record2 = Prefix::find($record1->id);
        $this->assertNotNull($record2);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record2->delete();
        $this->assertNull(Prefix::find($record1->id));
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

    public function testFieldName(): void
    {
        $record1 = TestFieldName::newInstance();
        $record1->abcDef = 'a1';
        $record1->aBCXYZ = 'b1';
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);
        $record2 = TestFieldName::find($record1->id);
        $this->assertEquals([
            'id'     => $record1->id,
            'abcDef' => 'a1',
            'aBCXYZ' => 'b1',
        ], $record2->toArray());

        $record1 = TestFieldName::newInstance([
            'abcDef' => 'a2',
            'aBCXYZ' => 'b2',
        ]);
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);
        $record2 = TestFieldName::find($record1->id);
        $this->assertEquals([
            'id'     => $record1->id,
            'abcDef' => 'a2',
            'aBCXYZ' => 'b2',
        ], $record2->toArray());

        $record1 = TestFieldName::newInstance([
            'Abc_Def' => 'a3',
            'ABC_XYZ' => 'b3',
        ]);
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);
        $record2 = TestFieldName::find($record1->id);
        $this->assertEquals([
            'id'     => $record1->id,
            'abcDef' => 'a3',
            'aBCXYZ' => 'b3',
        ], $record2->toArray());

        $record1 = TestFieldNameNotCamel::newInstance();
        $record1->abcDef = 'a1';
        $record1->aBCXYZ = 'b1';
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);
        $record2 = TestFieldNameNotCamel::find($record1->id);
        $this->assertEquals([
            'id'      => $record1->id,
            'Abc_Def' => 'a1',
            'ABC_XYZ' => 'b1',
        ], $record2->toArray());

        $record1 = TestFieldNameNotCamel::newInstance([
            'abcDef' => 'a2',
            'aBCXYZ' => 'b2',
        ]);
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);
        $record2 = TestFieldNameNotCamel::find($record1->id);
        $this->assertEquals([
            'id'      => $record1->id,
            'Abc_Def' => 'a2',
            'ABC_XYZ' => 'b2',
        ], $record2->toArray());

        $record1 = TestFieldNameNotCamel::newInstance([
            'Abc_Def' => 'a3',
            'ABC_XYZ' => 'b3',
        ]);
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);
        $record2 = TestFieldNameNotCamel::find($record1->id);
        $this->assertEquals([
            'id'      => $record1->id,
            'Abc_Def' => 'a3',
            'ABC_XYZ' => 'b3',
        ], $record2->toArray());
    }

    public function testBug403(): void
    {
        $record = TestBug403::newInstance([
            'json_data' => '[4, 5, 6]',
        ]);
        $this->assertEquals([
            'id' => null,
        ], $record->convertToArray());
        // @phpstan-ignore-next-line
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());
        $id = $record->insert()->getLastInsertId();
        $this->assertGreaterThan(0, $id);

        $record = TestBug403::find($id);
        $this->assertEquals([
            'id' => $id,
        ], $record->convertToArray());
        // @phpstan-ignore-next-line
        $this->assertEquals([4, 5, 6], $record->getJsonData()->toArray());

        $list = TestBug403::query()->where('id', '=', $id)->select()->getArray();
        $this->assertEquals([[
            'id' => $id,
        ]], TestJson::convertListToArray($list));

        $record = TestBug403::query()->where('id', '=', $id)->select()->get();
        $this->assertEquals([
            'id' => $id,
        ], $record->convertToArray());
    }

    public function testIncrUpdate(): void
    {
        $record1 = Article2::newInstance();
        $record1->memberId = 1024;
        $record1->title = __CLASS__;
        $record1->content = __FUNCTION__;
        $record1->insert();
        $this->assertGreaterThanOrEqual(1, $record1->id);

        $record2 = Article2::newInstance();
        $record2->memberId = 1024;
        $record2->title = __CLASS__;
        $record2->content = __FUNCTION__;
        $record2->save();
        $this->assertGreaterThanOrEqual(1, $record2->id);

        // 增量更新
        $updateSql = 'update `tb_article2` set `title` = :title where `id` = :p1 limit :p2';

        $record1->title = 't1';
        $result = $record1->update();
        $this->assertEquals($updateSql, $result->getSql());

        $record11 = Article2::find($record1->id);
        $this->assertEquals('t1', $record11->title);

        $record2->title = 't2';
        $result = $record2->save();
        $this->assertEquals($updateSql, $result->getSql());

        $record22 = Article2::find($record2->id);
        $this->assertEquals('t2', $record22->title);

        // 无修改不执行SQL
        $result = $record1->update();
        $this->assertEquals('', $result->getSql());

        $result = $record2->save();
        $this->assertEquals('', $result->getSql());
    }

    public function testSerialize(): void
    {
        $member = Member::newInstance();
        /** @var Member $member */
        $member = unserialize(serialize($member));
        $this->assertInstanceOf(Member::class, $member);
        $member->username = '1';
        $member->__setRaw('password', "CONCAT('p', 'w2')");
        $result = $member->insert();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());
        $id = $result->getLastInsertId();
        $this->assertGreaterThan(0, $id);
        $this->assertEquals($id, $member->id);

        $record = Member::find($member->id);
        $this->assertNotNull($record);
        $this->assertEquals('1', $record->username);
        $this->assertEquals('pw2', $record->password);
        /** @var Member $record2 */
        $record2 = unserialize(serialize($record));
        $this->assertEquals($record->toArray(), $record2->toArray());
        $this->assertEquals($record->username, $record2->username);
        $this->assertEquals($record->password, $record2->password);
    }

    public function testId(): void
    {
        // insert
        $record1 = ArticleId::newInstance();
        $record1->insert();
        $this->assertNotEmpty($record1->title);
        $this->assertNotEmpty($record1->content);
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = uuid_create(UUID_TYPE_RANDOM);
        $record1->update();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = uuid_create(UUID_TYPE_RANDOM);
        $record1->save();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        // save
        $record1 = ArticleId::newInstance();
        $record1->save();
        $this->assertNotEmpty($record1->title);
        $this->assertNotEmpty($record1->content);
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = uuid_create(UUID_TYPE_RANDOM);
        $record1->update();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = uuid_create(UUID_TYPE_RANDOM);
        $record1->save();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());
    }

    public function testIncAndDec(): void
    {
        $record = VirtualColumn::newInstance();
        $record->amount = 1;
        $record->insert();

        VirtualColumn::query()->where('id', '=', $record->id)
                              ->setFieldInc('amount')
                              ->update();
        $record = VirtualColumn::find($record->id);
        $this->assertEquals(2, $record->amount);

        VirtualColumn::query()->where('id', '=', $record->id)
                              ->setFieldDec('amount')
                              ->update();
        $record = VirtualColumn::find($record->id);
        $this->assertEquals(1, $record->amount);
    }

    public function testAnnotationDDL(): void
    {
        $ddl = new DDL('1+1');
        $this->assertEquals('1+1', $ddl->getRawSql());

        $ddl = new DDL('1+1', static fn (string $sql): string => '2' . $sql);
        $this->assertEquals('21+1', $ddl->getRawSql());
    }

    public function testAnnotationExtractProperty(): void
    {
        $record = TestJsonExtractProperty::newInstance();
        $record->jsonData = ['ex' => ['userId' => 123]];
        $record->insert();
        $record2 = TestJsonExtractProperty::find($record->id);
        $this->assertNotNull($record2);
        // @phpstan-ignore-next-line
        $this->assertEquals($record->jsonData, $record2->jsonData->toArray());
        $this->assertEquals(123, $record2->userId);
        $this->assertEquals(123, $record2->userId2);

        $record = TestJsonExtractProperty::newInstance();
        $record->jsonData = [];
        $record->insert();
        $record2 = TestJsonExtractProperty::find($record->id);
        $this->assertNotNull($record2);
        // @phpstan-ignore-next-line
        $this->assertEquals($record->jsonData, $record2->jsonData->toArray());
        $this->assertNull($record2->userId);
        $this->assertNull($record2->userId2);
    }

    public function testAnnotationEncodeDecode(): void
    {
        $record = TestJsonEncodeDecodeNone::newInstance();
        $record->jsonData = ['name' => '宇润'];
        $record->insert();
        $record2 = TestJsonEncodeDecodeNone::find($record->id);
        $this->assertNotNull($record2);
        $this->assertIsArray($record2->jsonData);
        $this->assertEquals('宇润', $record2->jsonData['name'] ?? null);

        $record = TestJsonEncodeDecodeCallable::newInstance();
        $record->jsonData = ['name' => '宇润'];
        $record->insert();
        $record2 = TestJsonEncodeDecodeCallable::find($record->id);
        $this->assertNotNull($record2);
        $this->assertIsArray($record2->jsonData);
        $this->assertEquals(['data' => ['name' => '宇润']], $record2->jsonData);

        $record = TestJsonEncodeDecode2::newInstance();
        $record->jsonData = ['name' => '宇润'];
        $record->insert();
        $record2 = TestJsonEncodeDecode2::find($record->id);
        $this->assertNotNull($record2);
        $this->assertIsObject($record2->jsonData);
        $this->assertEquals('宇润', $record2->jsonData->name ?? null);

        $record = TestJsonEncodeDecodeArraywarp::newInstance();
        $record->jsonData = [
            ['name' => '宇润'],
            ['name' => 'imi'],
        ];
        $record->insert();
        $record2 = TestJsonEncodeDecodeArraywarp::find($record->id);
        $this->assertNotNull($record2);
        $this->assertIsArray($record2->jsonData);
        $this->assertEquals('宇润', $record2->jsonData[0]['name'] ?? null);
        $this->assertEquals('imi', $record2->jsonData[1]['name'] ?? null);
    }
}
