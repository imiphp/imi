<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Db\Db;
use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Article;
use Imi\Test\Component\Model\Article2;
use Imi\Test\Component\Model\ArticleEx;
use Imi\Test\Component\Model\CreateTime;
use Imi\Test\Component\Model\Member;
use Imi\Test\Component\Model\MemberReferenceProperty;
use Imi\Test\Component\Model\MemberSerializable;
use Imi\Test\Component\Model\MemberWithSqlField;
use Imi\Test\Component\Model\Prefix;
use Imi\Test\Component\Model\ReferenceGetterTestModel;
use Imi\Test\Component\Model\TestEnum;
use Imi\Test\Component\Model\TestJson;
use Imi\Test\Component\Model\TestJsonNotCamel;
use Imi\Test\Component\Model\TestList;
use Imi\Test\Component\Model\TestSet;
use Imi\Test\Component\Model\TestSoftDelete;
use Imi\Test\Component\Model\TestWithMember;
use Imi\Test\Component\Model\UpdateTime;

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

    /**
     * @depends testInsert
     */
    public function testQuerySetField(array $args): void
    {
        ['id' => $id] = $args;
        /** @var Member $member */
        $member = Member::query()->field('username')->where('id', '=', $id)->select()->get();
        $this->assertEquals([
            'username' => '1',
        ], $member->toArray());

        $member = Member::newInstance(['username' => 'test']);
        $member->password = 'password';
        $member->insert();
        $id = $member->id;
        $this->assertEquals([
            'id'       => $id,
            'username' => 'test',
        ], $member->toArray());

        $member = Member::find($id);
        $this->assertEquals([
            'id'       => $id,
            'username' => 'test',
        ], $member->toArray());
        $this->assertEquals('password', $member->password);
    }

    public function testBatchUpdate(): void
    {
        $count1 = Member::count();
        $this->assertGreaterThan(0, $count1);

        $result = Member::updateBatch([
            'password' => '123',
        ]);
        $this->assertEquals($count1, $result->getAffectedRows());

        $list = Member::query()->select()->getColumn('password');
        $list = array_unique($list);
        $this->assertEquals(['123'], $list);
    }

    public function testBatchDelete(): void
    {
        $count1 = Member::count();
        $this->assertGreaterThan(0, $count1);

        $maxId = Member::max('id');
        $this->assertGreaterThan(0, $count1);

        // delete max id
        $result = Member::deleteBatch([
            'id' => $maxId,
        ]);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());

        $count2 = Member::count();
        $this->assertEquals($count1 - 1, $count2);

        // all delete
        $result = Member::deleteBatch();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals($count1 - 1, $result->getAffectedRows());

        $count3 = Member::count();
        $this->assertEquals(0, $count3);
    }

    private function assertUpdateTime(UpdateTime $record, string $methodName): void
    {
        $time = time();
        $bigintTime = (int) (microtime(true) * 1000);
        $result = $record->$methodName();
        $this->assertTrue($result->isSuccess());
        $this->assertLessThanOrEqual(1, strtotime($record->date) - strtotime(date('Y-m-d', $time)), sprintf('date fail: %s', $record->date));
        $this->assertLessThanOrEqual(1, strtotime($record->time) - strtotime(date('H:i:s', $time)), sprintf('time fail: %s', $record->time));
        $this->assertLessThanOrEqual(1, strtotime($record->datetime) - strtotime(date('Y-m-d H:i:s', $time)), sprintf('datetime fail: %s', $record->datetime));
        $this->assertLessThanOrEqual(1, strtotime($record->timestamp) - strtotime(date('Y-m-d H:i:s', $time)), sprintf('timestamp fail: %s', $record->timestamp));
        $this->assertLessThanOrEqual(1, $record->int - $time, sprintf('int fail: %s', $record->int));
        $this->assertLessThanOrEqual(1, $record->bigint - $bigintTime, sprintf('bigint fail: %s', $record->bigint));
        $this->assertLessThanOrEqual(1, $record->year - strtotime(date('Y', $time)), sprintf('year fail: %s', $record->year));
    }

    public function testUpdateTimeSave(): void
    {
        $this->go(function () {
            $record = UpdateTime::newInstance();
            $this->assertUpdateTime($record, 'save');
        }, null, 3);
    }

    public function testUpdateTimeUpdate(): void
    {
        $this->go(function () {
            $record = UpdateTime::find(1);
            $this->assertUpdateTime($record, 'update');
        }, null, 3);
    }

    private function assertCreateTime(CreateTime $record, string $methodName, bool $equals = true): void
    {
        $time = time();
        $bigintTime = (int) (microtime(true) * 1000);
        if (!$equals)
        {
            $oldArr = $record->toArray();
        }
        $result = $record->$methodName();
        $this->assertTrue($result->isSuccess());
        if ($equals)
        {
            $this->assertLessThanOrEqual(1, strtotime($record->date) - strtotime(date('Y-m-d', $time)), sprintf('date fail: %s', $record->date));
            $this->assertLessThanOrEqual(1, strtotime($record->time) - strtotime(date('H:i:s', $time)), sprintf('time fail: %s', $record->time));
            $this->assertLessThanOrEqual(1, strtotime($record->datetime) - strtotime(date('Y-m-d H:i:s', $time)), sprintf('datetime fail: %s', $record->datetime));
            $this->assertLessThanOrEqual(1, strtotime($record->timestamp) - strtotime(date('Y-m-d H:i:s', $time)), sprintf('timestamp fail: %s', $record->timestamp));
            $this->assertLessThanOrEqual(1, $record->int - $time, sprintf('int fail: %s', $record->int));
            $this->assertLessThanOrEqual(1, $record->bigint - $bigintTime, sprintf('bigint fail: %s', $record->bigint));
            $this->assertLessThanOrEqual(1, $record->year - strtotime(date('Y', $time)), sprintf('year fail: %s', $record->year));
        }
        else
        {
            $this->assertEquals($oldArr, $record->toArray());
        }
    }

    public function testCreateTimeInsert(): void
    {
        $this->go(function () {
            $record = CreateTime::newInstance();
            $this->assertCreateTime($record, 'insert');
            sleep(1);
            $this->assertCreateTime($record, 'update', false);
            $this->assertCreateTime($record, 'save', false);
        }, null, 3);
    }

    public function testCreateTimeSave(): void
    {
        $this->go(function () {
            $record = CreateTime::newInstance();
            $this->assertCreateTime($record, 'save');
            sleep(1);
            $this->assertCreateTime($record, 'update', false);
            $this->assertCreateTime($record, 'save', false);
        }, null, 3);
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

        $record2->update([
            'json_data->a' => 111,
        ]);
        $record2 = TestJson::find($record->id);
        $this->assertNotNull($record2);
        // @phpstan-ignore-next-line
        $this->assertEquals(['a' => 111, 'b' => 2, 'c' => 3], $record2->jsonData->toArray());
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

        $record = TestJsonNotCamel::query()->field('id', 'json_data')->where('id', '=', $id)->select()->get();
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
        $this->assertEquals(Article2::class, \get_class($record));
        $record->memberId = 1024;
        $record->title = __CLASS__;
        $record->content = __FUNCTION__;
        $record->save();
        $this->assertGreaterThan(0, $record->id);

        $record = Article2::find($record->id);
        $this->assertNotNull($record);
        $this->assertEquals(Article2::class, \get_class($record));
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
        $member1 = MemberReferenceProperty::query()->field('tb_member.*')->where('id', '=', $member->id)->select()->get();
        $this->assertNotNull($member1);
        $this->assertEquals($memberArray, $member1->toArray());

        /** @var MemberReferenceProperty|null $member1 */
        $list = MemberReferenceProperty::query()->field('tb_member.*')->where('id', '=', $member->id)->select()->getArray();
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
    public function testChunk(array $args): void
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
    public function testChunkEach(array $args): void
    {
        $data = [];
        foreach (Member::query()->chunkEach(32, 'id') as $item)
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
}
