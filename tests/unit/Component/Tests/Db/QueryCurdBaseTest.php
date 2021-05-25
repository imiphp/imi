<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db;

use Imi\Db\Db;
use Imi\Db\Query\Lock\MysqlLock;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @testdox QueryCurd
 */
abstract class QueryCurdBaseTest extends BaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName;

    /**
     * 测试 whereEx 的 SQL.
     *
     * @var string
     */
    protected $expectedTestWhereExSql;

    /**
     * 测试 JSON 查询的 SQL.
     *
     * @var string
     */
    protected $expectedTestJsonSelectSql;

    public function testSelectGet(): void
    {
        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->where('id', '=', 1)->select()->get();
        Assert::assertEquals([
            'id'        => '1',
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $record);
    }

    public function testSelectGetArray(): void
    {
        $query = Db::query($this->poolName);
        $result = $query->from('tb_article')->whereIn('id', [1, 2])->select();
        $record = $result->getArray();
        Assert::assertEquals(2, $result->getRowCount());
        Assert::assertEquals([
            [
                'id'        => '1',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
            [
                'id'        => '2',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $record);
    }

    public function testSelectGetColumn(): void
    {
        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->whereIn('id', [1, 2])->select()->getColumn();
        Assert::assertEquals(['1', '2'], $record);
    }

    public function testSelectGetScalar(): void
    {
        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->where('id', '=', 2)->field('id')->select()->getScalar();
        Assert::assertEquals(2, $record);
    }

    public function testPaginate(): void
    {
        $expectedData = [
            'list'  => [
                [
                    'id'        => '2',
                    'title'     => 'title',
                    'content'   => 'content',
                    'time'      => '2019-06-21 00:00:00',
                ],
            ],
            'limit'         => 1,
            'total'         => 3,
            'page_count'    => 3,
        ];
        $query = Db::query($this->poolName);
        $result = $query->from('tb_article')->paginate(2, 1);
        $this->assertEquals($expectedData, $result->toArray());
        $this->assertEquals($expectedData['list'], $result->getList());
        $this->assertEquals($expectedData['total'], $result->getTotal());
        $this->assertEquals($expectedData['limit'], $result->getLimit());
        $this->assertEquals($expectedData['page_count'], $result->getPageCount());
    }

    /**
     * @see https://github.com/Yurunsoft/imi/issues/58
     */
    public function testBug58(): void
    {
        $expectedData = [
            'list'  => [
                [
                    'id'        => '2',
                    'title'     => 'title',
                    'content'   => 'content',
                    'time'      => '2019-06-21 00:00:00',
                ],
            ],
            'limit'         => 1,
            'total'         => 1,
            'page_count'    => 1,
        ];
        $result = Db::query($this->poolName)->from('tb_article')
                             ->bindValues([
                                 ':id'  => 2,
                             ])
                             ->whereRaw('id = :id')
                             ->paginate(1, 1);
        $this->assertEquals($expectedData, $result->toArray());
    }

    public function testPaginateNoTotal(): void
    {
        $expectedData = [
            'list'  => [
                [
                    'id'        => '1',
                    'title'     => 'title',
                    'content'   => 'content',
                    'time'      => '2019-06-21 00:00:00',
                ],
                [
                    'id'        => '2',
                    'title'     => 'title',
                    'content'   => 'content',
                    'time'      => '2019-06-21 00:00:00',
                ],
            ],
            'limit'         => 2,
        ];
        $query = Db::query($this->poolName);
        $result = $query->from('tb_article')->paginate(1, 2, [
            'total' => false,
        ]);
        $this->assertEquals($expectedData, $result->toArray());
        $this->assertEquals([
            [
                'id'        => '1',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
            [
                'id'        => '2',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $result->getList());
        $this->assertNull($result->getTotal());
        $this->assertEquals(2, $result->getLimit());
        $this->assertNull($result->getPageCount());
    }

    public function testInsert(): void
    {
        $data = [
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ];
        $query = Db::query($this->poolName);

        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id . '',
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ], $record);

        $result = $query->setData([
            'title'     => 'title-insert',
            'content'   => 'content-insert',
        ])
        ->setField('time', '2019-06-21 00:00:00')
        ->from('tb_article')
        ->insert();
        $id = $result->getLastInsertId();
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id . '',
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ], $record);
    }

    public function testUpdate(): void
    {
        $data = [
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ];
        $query = Db::query($this->poolName);
        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();

        $result = $query->from('tb_article')->where('id', '=', $id)->update([
            'content'   => 'imi',
            'time'      => '2018-06-21 00:00:00',
        ]);
        Assert::assertEquals(1, $result->getAffectedRows());
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id . '',
            'title'     => 'title-insert',
            'content'   => 'imi',
            'time'      => '2018-06-21 00:00:00',
        ], $record);

        $result = $query->from('tb_article')->where('id', '=', $id)->setData([
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ])->update();
        Assert::assertEquals(1, $result->getAffectedRows());
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id . '',
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ], $record);
    }

    public function testDelete(): void
    {
        $data = [
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ];
        $query = Db::query($this->poolName);
        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();

        $result = $query->from('tb_article')->where('id', '=', $id)->delete();
        Assert::assertEquals(1, $result->getAffectedRows());

        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertNull($record);
    }

    public function testWhereEx(): void
    {
        $query = Db::query($this->poolName);
        $result = $query->from('tb_article')->whereEx([
            'id'    => 1,
            'and'   => [
                'id'    => ['in', [1]],
            ],
        ])->select();
        // 多条件SQL
        Assert::assertEquals($this->expectedTestWhereExSql, $result->getSql());
        // 查询记录
        $record = $result->get();
        Assert::assertEquals([
            'id'        => '1',
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $record);
        // BUG: https://github.com/Yurunsoft/IMI/pull/25
        Assert::assertEquals('select * from `tb_article`', Db::query($this->poolName)->from('tb_article')->whereEx([])->select()->getSql());
    }

    public function testLock(): void
    {
        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->where('id', '=', 1)->lock(MysqlLock::FOR_UPDATE)->select()->get();
        Assert::assertEquals([
            'id'        => '1',
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $record);

        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->where('id', '=', 1)->lock(MysqlLock::SHARED)->select()->get();
        Assert::assertEquals([
            'id'        => '1',
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $record);
    }

    public function testRawAlias()
    {
        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->whereIsNotNull('id')->field('id')->fieldRaw('title')->fieldRaw('id + 1', 'id2')->select()->get();
        Assert::assertEquals([
            'id'    => '1',
            'title' => 'title',
            'id2'   => '2',
        ], $record);
    }

    public function testJson()
    {
        $query = Db::query($this->poolName);
        $jsonStr = '{"uid": "' . ($uid = uniqid('', true)) . '", "name": "aaa", "list1": [{"id": 1}]}';
        // 插入数据
        $insertResult = $query->from('tb_test_json')->insert([
            'json_data' => $jsonStr,
        ]);
        $id = $insertResult->getLastInsertId();
        // 查询条件
        $result = $query->from('tb_test_json')->where('json_data->uid', '=', $uid)->select();
        $this->assertEquals([
            'id'        => $id,
            'json_data' => $jsonStr,
        ], $result->get());
        $this->assertEquals($this->expectedTestJsonSelectSql, $result->getSql());
        // 更新数据
        $query->from('tb_test_json')->where('json_data->uid', '=', $uid)->update([
            'json_data->a'           => '1',
            'json_data->name'        => 'bbb',
            'json_data->list1[0].id' => '2',
            'json_data->list2'       => [1, 2, 3],
        ]);
        $result = $query->from('tb_test_json')->where('json_data->uid', '=', $uid)->order('json_data->uid')->select();
        $this->assertEquals([
            'id'        => $id,
            'json_data' => '{"a": "1", "uid": "' . $uid . '", "name": "bbb", "list1": [{"id": "2"}], "list2": [1, 2, 3]}',
        ], $result->get());
    }
}
