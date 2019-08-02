<?php
namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Member;

/**
 * @testdox Model
 */
class ModelTest extends BaseTest
{
    public function testInsert()
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

    public function testUpdate()
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
            'id'        =>  $id,
            'username'  =>  '3',
            'password'  =>  '4',
        ], $member->toArray());
    }

    public function testSave()
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
        $this->assertEquals(2, $result->getAffectedRows());

        $member = Member::find($id);
        $this->assertEquals([
            'id'        =>  $id,
            'username'  =>  '3',
            'password'  =>  '4',
        ], $member->toArray());
    }

    public function testDelete()
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

    public function testFind()
    {
        $member = Member::find(1);
        $this->assertEquals([
            'id'        =>  1,
            'username'  =>  '1',
            'password'  =>  '2',
        ], $member->toArray());
    }

    public function testSelect()
    {
        $list = Member::select([
            'id'    =>    1
        ]);
        $this->assertEquals([
            [
                'id'        =>  1,
                'username'  =>  '1',
                'password'  =>  '2',
            ]
        ], json_decode(json_encode($list), true));
    }

    public function testBatchUpdate()
    {
        $count1 = Member::count();
        $this->assertGreaterThan(0, $count1);

        $result = Member::updateBatch([
            'password'  =>  '123',
        ]);
        $this->assertEquals($count1, $result->getAffectedRows());

        $list = Member::query()->select()->getColumn('password');
        $list = array_unique($list);
        $this->assertEquals(['123'], $list);
    }

    public function testBatchDelete()
    {
        $count1 = Member::count();
        $this->assertGreaterThan(0, $count1);

        $maxId = Member::max('id');
        $this->assertGreaterThan(0, $count1);

        // delete max id
        $result = Member::deleteBatch([
            'id'    =>  $maxId,
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

}
