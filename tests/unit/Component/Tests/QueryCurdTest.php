<?php
namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\App;
use Imi\Db\Db;
use PHPUnit\Framework\Assert;

/**
 * @testdox QueryCurd
 */
class QueryCurdTest extends BaseTest
{
    public function testSelectGet()
    {
        $query = Db::query();
        $record = $query->from('tb_article')->where('id', '=', 1)->select()->get();
        Assert::assertEquals([
            'id'        =>  '1',
            'title'     =>  'title',
            'content'   =>  'content',
            'time'      =>  '2019-06-21 00:00:00',
        ], $record);
    }

    public function testSelectGetArray()
    {
        $query = Db::query();
        $result = $query->from('tb_article')->whereIn('id', [1, 2])->select();
        $record = $result->getArray();
        Assert::assertEquals(2, $result->getRowCount());
        Assert::assertEquals([
            [
                'id'        =>  '1',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ],
            [
                'id'        =>  '2',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ]
        ], $record);
    }

    public function testSelectGetColumn()
    {
        $query = Db::query();
        $record = $query->from('tb_article')->whereIn('id', [1, 2])->select()->getColumn();
        Assert::assertEquals(['1', '2'], $record);
    }

    public function testSelectGetScalar()
    {
        $query = Db::query();
        $record = $query->from('tb_article')->where('id', '=', 2)->field('id')->select()->getScalar();
        Assert::assertEquals(2, $record);
    }

    public function testInsert()
    {
        $data = [
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ];
        $query = Db::query();

        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        =>  $id . '',
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ], $record);

        $result = $query->setData([
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
        ])
        ->setField('time', '2019-06-21 00:00:00')
        ->from('tb_article')
        ->insert();
        $id = $result->getLastInsertId();
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        =>  $id . '',
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ], $record);

    }

    public function testUpdate()
    {
        $data = [
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ];
        $query = Db::query();
        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();

        $result = $query->from('tb_article')->where('id', '=', $id)->update([
            'content'   =>  'imi',
            'time'      =>  '2018-06-21 00:00:00',
        ]);
        Assert::assertEquals(1, $result->getAffectedRows());
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        =>  $id . '',
            'title'     =>  'title-insert',
            'content'   =>  'imi',
            'time'      =>  '2018-06-21 00:00:00',
        ], $record);

        $result = $query->from('tb_article')->where('id', '=', $id)->setData([
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ])->update();
        Assert::assertEquals(1, $result->getAffectedRows());
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        =>  $id . '',
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ], $record);

    }

    public function testDelete()
    {
        $data = [
            'title'     =>  'title-insert',
            'content'   =>  'content-insert',
            'time'      =>  '2019-06-21 00:00:00',
        ];
        $query = Db::query();
        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();

        $result = $query->from('tb_article')->where('id', '=', $id)->delete();
        Assert::assertEquals(1, $result->getAffectedRows());

        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertNull($record);
    }

}
