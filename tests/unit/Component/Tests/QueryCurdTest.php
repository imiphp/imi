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

    public function testPaginate()
    {
        $expectedData = [
            'list'  =>  [
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
                ],
            ],
            'limit'         =>  2,
            'total'         =>  3,
            'page_count'    =>  2,
        ];
        $query = Db::query();
        $result = $query->from('tb_article')->paginate(1, 2);
        $this->assertEqualsCanonicalizing($expectedData, $result->toArray());
        $this->assertEqualsCanonicalizing(json_encode($expectedData), json_encode($result));
        $this->assertEqualsCanonicalizing([
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
            ],
        ], $result->getList());
        $this->assertEquals(3, $result->getTotal());
        $this->assertEquals(2, $result->getLimit());
        $this->assertEquals(2, $result->getPageCount());
    }

    public function testPaginateNoTotal()
    {
        $expectedData = [
            'list'  =>  [
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
                ],
            ],
            'limit'         =>  2,
        ];
        $query = Db::query();
        $result = $query->from('tb_article')->paginate(1, 2, [
            'total' =>  false,
        ]);
        var_dump($result->toArray());
        $this->assertEqualsCanonicalizing($expectedData, $result->toArray());
        $this->assertEqualsCanonicalizing(json_encode($expectedData), json_encode($result));
        $this->assertEqualsCanonicalizing([
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
            ],
        ], $result->getList());
        $this->assertNull($result->getTotal());
        $this->assertEquals(2, $result->getLimit());
        $this->assertNull($result->getPageCount());
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

    public function testWhereEx()
    {
        $query = Db::query();
        $result = $query->from('tb_article')->whereEx([
            'id'    =>  1,
            'and'   =>  [
                'id'    =>  ['in', [1]],
            ],
        ])->select();
        // 多条件SQL
        Assert::assertEquals('select * from `tb_article` where (`id` = :p1 and (`id` in (:p2) ) )', $result->getSql());
        // 查询记录
        $record = $result->get();
        Assert::assertEquals([
            'id'        =>  '1',
            'title'     =>  'title',
            'content'   =>  'content',
            'time'      =>  '2019-06-21 00:00:00',
        ], $record);
        // BUG: https://github.com/Yurunsoft/IMI/pull/25
        Assert::assertEquals('select * from `tb_article`', Db::query()->from('tb_article')->whereEx([])->select()->getSql());
    }
}
