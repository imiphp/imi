<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db;

use Imi\Db\Db;
use Imi\Db\Mysql\Query\Lock\MysqlLock;
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
    protected ?string $poolName = null;

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

    protected string $fullTableArticle = 'tb_article';

    protected string $tableArticle = 'tb_article';

    protected string $fullTableTestJson = 'tb_test_json';

    protected string $tableTestJson = 'tb_test_json';

    public function testInsert(): array
    {
        Db::getInstance()->exec('truncate ' . $this->fullTableArticle);
        $data = [
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ];
        $query = Db::query($this->poolName);

        $result = $query->from($this->tableArticle)->insert($data);
        $id1 = $result->getLastInsertId();
        $record = $query->from($this->tableArticle)->where('id', '=', $id1)->select()->get();
        Assert::assertEquals([
            'id'        => $id1,
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);

        $result = $query->setData([
            'title'     => 'title-insert',
            'content'   => 'content-insert',
        ])
        ->setField('time', '2019-06-21 00:00:00')
        ->from($this->tableArticle)
        ->insert();
        $id2 = $result->getLastInsertId();
        $record = $query->from($this->tableArticle)->where('id', '=', $id2)->select()->get();
        Assert::assertEquals([
            'id'        => $id2,
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);

        return [
            'ids' => [$id1, $id2],
        ];
    }

    /**
     * @depends testInsert
     */
    public function testSelectGet(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $record = $query->from($this->tableArticle)->where('id', '=', $ids[0])->select()->get();
        Assert::assertEquals([
            'id'        => $ids[0],
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);
    }

    /**
     * @depends testInsert
     */
    public function testSelectGetArray(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $result = $query->from($this->tableArticle)->whereIn('id', $ids)->select();
        $record = $result->getArray();
        Assert::assertEquals(2, $result->getRowCount());
        Assert::assertEquals([
            [
                'id'        => $ids[0],
                'title'     => 'title-insert',
                'content'   => 'content-insert',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
            [
                'id'        => $ids[1],
                'title'     => 'title-insert',
                'content'   => 'content-insert',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $record);
    }

    /**
     * @depends testInsert
     */
    public function testSelectGetColumn(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $record = $query->from($this->tableArticle)->whereIn('id', $ids)->select()->getColumn();
        Assert::assertEquals($ids, $record);
    }

    /**
     * @depends testInsert
     */
    public function testSelectGetScalar(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $record = $query->from($this->tableArticle)->where('id', '=', $ids[1])->field('id')->select()->getScalar();
        Assert::assertEquals($ids[1], $record);
    }

    /**
     * @depends testInsert
     */
    public function testPaginate(array $args): void
    {
        ['ids' => $ids] = $args;
        $expectedData = [
            'list'  => [
                [
                    'id'        => $ids[1],
                    'title'     => 'title-insert',
                    'content'   => 'content-insert',
                    'time'      => '2019-06-21 00:00:00',
                    'member_id' => 0,
                ],
            ],
            'limit'         => 1,
            'total'         => 2,
            'page_count'    => 2,
        ];
        $query = Db::query($this->poolName);
        $result = $query->from($this->tableArticle)->paginate(2, 1);
        $this->assertEquals($expectedData, $result->toArray());
        $this->assertEquals($expectedData['list'], $result->getList());
        $this->assertEquals($expectedData['total'], $result->getTotal());
        $this->assertEquals($expectedData['limit'], $result->getLimit());
        $this->assertEquals($expectedData['page_count'], $result->getPageCount());
    }

    /**
     * @depends testInsert
     *
     * @see https://github.com/imiphp/imi/issues/58
     */
    public function testBug58(array $args): void
    {
        ['ids' => $ids] = $args;
        $expectedData = [
            'list'  => [
                [
                    'id'        => $ids[1],
                    'title'     => 'title-insert',
                    'content'   => 'content-insert',
                    'time'      => '2019-06-21 00:00:00',
                    'member_id' => 0,
                ],
            ],
            'limit'         => 1,
            'total'         => 1,
            'page_count'    => 1,
        ];
        $result = Db::query($this->poolName)->from($this->tableArticle)
                             ->bindValues([
                                 ':id'  => $ids[1],
                             ])
                             ->whereRaw('id = :id')
                             ->paginate(1, 1);
        $this->assertEquals($expectedData, $result->toArray());
    }

    /**
     * @depends testInsert
     */
    public function testPaginateNoTotal(array $args): void
    {
        ['ids' => $ids] = $args;
        $expectedData = [
            'list'  => [
                [
                    'id'        => $ids[0],
                    'title'     => 'title-insert',
                    'content'   => 'content-insert',
                    'time'      => '2019-06-21 00:00:00',
                    'member_id' => 0,
                ],
                [
                    'id'        => $ids[1],
                    'title'     => 'title-insert',
                    'content'   => 'content-insert',
                    'time'      => '2019-06-21 00:00:00',
                    'member_id' => 0,
                ],
            ],
            'limit'         => 2,
        ];
        $query = Db::query($this->poolName);
        $result = $query->from($this->tableArticle)->paginate(1, 2, [
            'total' => false,
        ]);
        $this->assertEquals($expectedData, $result->toArray());
        $this->assertEquals([
            [
                'id'        => $ids[0],
                'title'     => 'title-insert',
                'content'   => 'content-insert',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
            [
                'id'        => $ids[1],
                'title'     => 'title-insert',
                'content'   => 'content-insert',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $result->getList());
        $this->assertNull($result->getTotal());
        $this->assertEquals(2, $result->getLimit());
        $this->assertNull($result->getPageCount());
    }

    public function testUpdate(): void
    {
        $data = [
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ];
        $query = Db::query($this->poolName);
        $result = $query->from($this->tableArticle)->insert($data);
        $id = $result->getLastInsertId();

        $result = $query->from($this->tableArticle)->where('id', '=', $id)->update([
            'content'   => 'imi',
            'time'      => '2018-06-21 00:00:00',
        ]);
        Assert::assertEquals(1, $result->getAffectedRows());
        $record = $query->from($this->tableArticle)->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id,
            'title'     => 'title-insert',
            'content'   => 'imi',
            'time'      => '2018-06-21 00:00:00',
            'member_id' => 0,
        ], $record);

        $result = $query->from($this->tableArticle)->where('id', '=', $id)->setData([
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
        ])->update();
        Assert::assertEquals(1, $result->getAffectedRows());
        $record = $query->from($this->tableArticle)->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id,
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
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
        $result = $query->from($this->tableArticle)->insert($data);
        $id = $result->getLastInsertId();

        $result = $query->from($this->tableArticle)->where('id', '=', $id)->delete();
        Assert::assertEquals(1, $result->getAffectedRows());

        $record = $query->from($this->tableArticle)->where('id', '=', $id)->select()->get();
        Assert::assertNull($record);
    }

    /**
     * @depends testInsert
     */
    public function testWhereEx(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $result = $query->from($this->tableArticle)->whereEx([
            'id'    => $ids[0],
            'and'   => [
                'id'    => ['in', [$ids[0]]],
            ],
        ])->select();
        // 多条件SQL
        Assert::assertEquals($this->expectedTestWhereExSql, $result->getSql());
        // 查询记录
        $record = $result->get();
        Assert::assertEquals([
            'id'        => $ids[0],
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);
        // BUG: https://github.com/imiphp/imi/pull/25
        Assert::assertEquals('select * from `' . $this->fullTableArticle . '`', Db::query($this->poolName)->from($this->tableArticle)->whereEx([])->select()->getSql());
    }

    /**
     * @depends testInsert
     */
    public function testLock(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $record = $query->from($this->tableArticle)->where('id', '=', 1)->lock(MysqlLock::FOR_UPDATE)->select()->get();
        Assert::assertEquals([
            'id'        => $ids[0],
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);

        $query = Db::query($this->poolName);
        $record = $query->from($this->tableArticle)->where('id', '=', 1)->lock(MysqlLock::SHARED)->select()->get();
        Assert::assertEquals([
            'id'        => $ids[0],
            'title'     => 'title-insert',
            'content'   => 'content-insert',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);
    }

    /**
     * @depends testInsert
     */
    public function testRawAlias(array $args): void
    {
        ['ids' => $ids] = $args;
        $query = Db::query($this->poolName);
        $record = $query->from($this->tableArticle)->whereIsNotNull('id')->field('id')->fieldRaw('title')->fieldRaw('id + 1', 'id2')->select()->get();
        Assert::assertEquals([
            'id'    => $ids[0],
            'title' => 'title-insert',
            'id2'   => $ids[0] + 1,
        ], $record);
    }

    public function testJson(): void
    {
        $query = Db::query($this->poolName);
        $jsonStr = '{"uid": "' . ($uid = uniqid('', true)) . '", "name": "aaa", "list1": [{"id": 1}]}';
        // 插入数据
        $insertResult = $query->from($this->tableTestJson)->insert([
            'json_data' => $jsonStr,
        ]);
        $id = $insertResult->getLastInsertId();
        // 查询条件
        $result = $query->from($this->tableTestJson)->where('json_data->uid', '=', $uid)->select();
        $this->assertEquals([
            'id'        => $id,
            'json_data' => $jsonStr,
        ], $result->get());
        $this->assertEquals($this->expectedTestJsonSelectSql, $result->getSql());
        // 更新数据
        $query->from($this->tableTestJson)->where('json_data->uid', '=', $uid)->update([
            'json_data->a'           => '1',
            'json_data->name'        => 'bbb',
            'json_data->list1[0].id' => '2',
            'json_data->list2'       => [1, 2, 3],
        ]);
        $result = $query->from($this->tableTestJson)->where('json_data->uid', '=', $uid)->order('json_data->uid')->select();
        $this->assertEquals([
            'id'        => $id,
            'json_data' => '{"a": "1", "uid": "' . $uid . '", "name": "bbb", "list1": [{"id": "2"}], "list2": [1, 2, 3]}',
        ], $result->get());
    }
}
