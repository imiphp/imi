<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Member;
use Imi\Test\Component\Model\MemberWithSqlField;
use Imi\Test\Component\Model\ReferenceGetterTestModel;
use Imi\Test\Component\Model\TestJson;
use Imi\Test\Component\Model\TestJsonNotCamel;
use Imi\Test\Component\Model\TestList;
use Imi\Test\Component\Model\TestSoftDelete;
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

    public function testQuerySetField(): void
    {
        /** @var Member $member */
        $member = Member::query()->field('username')->where('id', '=', 1)->select()->get();
        $this->assertEquals([
            'username'  => '1',
        ], $member->toArray());
    }

    public function testBatchUpdate(): void
    {
        $count1 = Member::count();
        $this->assertGreaterThan(0, $count1);

        $result = Member::updateBatch([
            'password'  => '123',
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
            'id'    => $maxId,
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
        for ($_ = 0; $_ < 3; ++$_)
        {
            try
            {
                $th = null;
                $record = UpdateTime::newInstance();
                $this->assertUpdateTime($record, 'save');
            }
            catch (\Throwable $th)
            {
                sleep(1);
            }
        }
        if (isset($th))
        {
            throw $th;
        }
    }

    public function testUpdateTimeUpdate(): void
    {
        for ($_ = 0; $_ < 3; ++$_)
        {
            try
            {
                $th = null;
                $record = UpdateTime::find(1);
                $this->assertUpdateTime($record, 'update');
            }
            catch (\Throwable $th)
            {
                sleep(1);
            }
        }
        if (isset($th))
        {
            throw $th;
        }
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
    }
}
