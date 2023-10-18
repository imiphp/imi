<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db;

use Imi\Db\Db;
use Imi\Pgsql\Db\Query\FullText\PgsqlFullTextOptions;
use Imi\Pgsql\Db\Query\FullText\TsQuery;
use Imi\Pgsql\Db\Query\FullText\TsRank;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @testdox QueryCurd
 */
abstract class QueryCurdBaseTest extends TestCase
{
    /**
     * 连接池名.
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
            'list'          => [
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
     * @see https://github.com/imiphp/imi/issues/58
     */
    public function testBug58(): void
    {
        $expectedData = [
            'list'          => [
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
            'list'          => [
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

    public function testInsert(): array
    {
        $data = [
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ];
        $query = Db::query($this->poolName);

        $result = $query->from('tb_article')->insert($data);
        $id = $result->getLastInsertId();
        $this->assertGreaterThan(0, $id);
        // lastInsertId 传 name 测试
        $this->assertEquals($id, $query->getDb()->lastInsertId('tb_article_id_seq'));
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id . '',
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $record);

        $result = $query->setData([
            'title'     => 'title',
            'content'   => 'content',
        ])
        ->setField('time', '2019-06-21 00:00:00')
        ->from('tb_article')
        ->insert();
        $id = $result->getLastInsertId();
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id . '',
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $record);

        return [
            'id' => $id,
        ];
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
        // BUG: https://github.com/imiphp/imi/pull/25
        Assert::assertEquals('select * from "tb_article"', Db::query($this->poolName)->from('tb_article')->whereEx([])->select()->getSql());
    }

    public function testRawAlias(): void
    {
        $query = Db::query($this->poolName);
        $record = $query->from('tb_article')->whereIsNotNull('id')->field('id')->fieldRaw('title')->fieldRaw('id + 1', 'id2')->select()->get();
        Assert::assertEquals([
            'id'    => '1',
            'title' => 'title',
            'id2'   => '2',
        ], $record);
    }

    public function testJson(): void
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

    public function testRawBinds(): void
    {
        $query = Db::query()->from('test')
                            ->fieldRaw('test.*, ?', null, ['imi'])
                            ->joinRaw('join test2 on test.id = test2.id and test2.id2 = ?', [1])
                            ->whereRaw('test.id = ?', 'and', [2])
                            ->orWhereRaw('test.id = ?', [3])
                            ->groupRaw('test.id, ?', [4])
                            ->havingRaw('test.id = ?', 'and', [5])
                            ->orderRaw('field(test.id, ?, ?)', [6, 7])
        ;
        $this->assertEquals('select test.*, ? from "test" join test2 on test.id = test2.id and test2.id2 = ? where test.id = ? or test.id = ? group by test.id, ? having test.id = ? order by field(test.id, ?, ?)', $query->buildSelectSql());
        $this->assertEquals(['imi', 1, 2, 3, 4, 5, 6, 7], $query->getBinds());
    }

    public function testSetFieldExp(): void
    {
        $query = Db::query()->from('test')->setFieldExp('c', '1 + ?', [1])
        ;
        $this->assertEquals('insert into "test"("c") values(1 + ?)', $query->buildInsertSql());
        $this->assertEquals([1], $query->getBinds());

        $query = Db::query()->from('test')->setFieldInc('a', 1)
                                          ->setFieldDec('b', 2)
                                          ->setFieldExp('c', 'c + :c', [':c' => 3])
        ;
        $this->assertEquals('update "test" set "a" = "a" + :fip1,"b" = "b" - :fdp2,"c" = c + :c', $query->buildUpdateSql());
        $this->assertEquals([':fip1' => 1, ':fdp2' => 2, ':c' => 3], $query->getBinds());
    }

    /**
     * @depends testInsert
     */
    public function testStatementClose(array $args): void
    {
        ['id' => $id] = $args;
        $db = Db::getInstance($this->poolName);
        $stmt = $db->query('select * from tb_article where id = ' . $id);
        $stmt->close();
        $this->assertTrue(true);
    }

    /**
     * @depends testInsert
     */
    public function testQueryAlias(array $args): void
    {
        ['id' => $id] = $args;
        $result = Db::getInstance($this->poolName)
            ->createQuery()
            ->table('tb_article', 'a1')
            ->where('a1.id', '=', $id)
            ->select()
            ->getArray();
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $result);
    }

    /**
     * @depends testInsert
     */
    public function testFind(array $args): void
    {
        ['id' => $id] = $args;
        $result = Db::query()
            ->table('tb_article')
            ->where('id', '=', $id)
            ->find();
        Assert::assertEquals([
            'id'        => $id,
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
        ], $result);
    }

    /**
     * @depends testInsert
     */
    public function testSelect(array $args): void
    {
        ['id' => $id] = $args;
        $result = Db::select('select * from tb_article where id = ' . $id, [], $this->poolName);
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $result->getArray());

        $result = Db::select('select * from tb_article where id = ?', [$id], $this->poolName);
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $result->getArray());
    }

    /**
     * @depends testInsert
     */
    public function testValue(array $args): void
    {
        ['id' => $id] = $args;

        $value = Db::query($this->poolName)
            ->table('tb_article')
            ->where('id', '=', $id)
            ->value('title');
        $this->assertEquals('title', $value);

        $value = Db::query($this->poolName)
            ->table('tb_article')
            ->where('id', '=', $id)
            ->value('time');
        $this->assertEquals('2019-06-21 00:00:00', $value);

        $value = Db::query($this->poolName)
            ->table('tb_article')
            ->where('id', '=', -1)
            ->value('id', '9999999');
        $this->assertEquals('9999999', $value);
    }

    public function testFullTextSearch(): void
    {
        $query = Db::query()->from('test')->fullText('content', 'imi');
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p1)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi'], $query->getBinds());

        $query = Db::query()->from('test')->fullText(['title', 'content'], 'imi');
        $this->assertEquals('select * from "test" where (to_tsvector("title")||to_tsvector("content")) @@ plainto_tsquery(:p1)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi'], $query->getBinds());

        $query = Db::query()->from('test')->where('member_id', '=', 1)->fullText('content', 'imi');
        $this->assertEquals('select * from "test" where "member_id" = :p1 and (to_tsvector("content")) @@ plainto_tsquery(:p2)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 1, ':p2' => 'imi'], $query->getBinds());

        $query = Db::query()->from('test')->where('member_id', '=', 1)->fullText(['title', 'content'], 'imi', $options = (new PgsqlFullTextOptions())->setWhereLogicalOperator('or'));
        $this->assertEquals('select * from "test" where "member_id" = :p1 or (to_tsvector("title")||to_tsvector("content")) @@ plainto_tsquery(:p2)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 1, ':p2' => 'imi'], $query->getBinds());
        $this->assertEquals('or', $options->getWhereLogicalOperator());

        $query = Db::query()->from('test')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setLanguage('simple'));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p1,:p2)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'simple', ':p2' => 'imi'], $query->getBinds());
        $this->assertEquals('simple', $options->getLanguage());

        $query = Db::query()->from('test')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setTsQueryFunction(TsQuery::TO_TSQUERY));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ to_tsquery(:p1)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi'], $query->getBinds());
        $this->assertEquals(TsQuery::TO_TSQUERY, $options->getTsQueryFunction());

        $query = Db::query()->from('test')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setTsQueryFunction(TsQuery::PHRASETO_TSQUERY));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ phraseto_tsquery(:p1)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi'], $query->getBinds());
        $this->assertEquals(TsQuery::PHRASETO_TSQUERY, $options->getTsQueryFunction());

        $query = Db::query()->from('test')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setTsQueryFunction(TsQuery::WEBSEARCH_TO_TSQUERY));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ websearch_to_tsquery(:p1)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi'], $query->getBinds());
        $this->assertEquals(TsQuery::WEBSEARCH_TO_TSQUERY, $options->getTsQueryFunction());

        $query = Db::query()->from('test')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setMinScore(0.25));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p1) AND ts_rank_cd(to_tsvector("content"), plainto_tsquery(:p3)) >= :p2', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi', ':p2' => 0.25, ':p3' => 'imi'], $query->getBinds());
        $this->assertEquals(0.25, $options->getMinScore());

        $query = Db::query()->from('test')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setMinScore(0.25)->setTsRankFunction(TsRank::TS_RANK));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p1) AND ts_rank(to_tsvector("content"), plainto_tsquery(:p3)) >= :p2', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi', ':p2' => 0.25, ':p3' => 'imi'], $query->getBinds());
        $this->assertEquals(0.25, $options->getMinScore());
        $this->assertEquals(TsRank::TS_RANK, $options->getTsRankFunction());

        $query = Db::query()->from('test')->field('*')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setScoreFieldName(''));
        $this->assertEquals('select *,ts_rank_cd(to_tsvector("content"), plainto_tsquery(:p1)) from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p2)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi', ':p2' => 'imi'], $query->getBinds());
        $this->assertEquals('', $options->getScoreFieldName());

        $query = Db::query()->from('test')->field('*')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setScoreFieldName('score'));
        $this->assertEquals('select *,(ts_rank_cd(to_tsvector("content"), plainto_tsquery(:p1))) as "score" from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p2)', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi', ':p2' => 'imi'], $query->getBinds());
        $this->assertEquals('score', $options->getScoreFieldName());

        $query = Db::query()->from('test')->field('*')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setOrderDirection('desc'));
        $this->assertEquals('select * from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p2) order by (ts_rank_cd(to_tsvector("content"), plainto_tsquery(:p1))) desc', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi', ':p2' => 'imi'], $query->getBinds());
        $this->assertEquals('desc', $options->getOrderDirection());

        $query = Db::query()->from('test')->field('*')->fullText('content', 'imi', $options = (new PgsqlFullTextOptions())->setScoreFieldName('score')->setOrderDirection('desc'));
        $this->assertEquals('select *,(ts_rank_cd(to_tsvector("content"), plainto_tsquery(:p1))) as "score" from "test" where (to_tsvector("content")) @@ plainto_tsquery(:p2) order by "score" desc', $query->buildSelectSql());
        $this->assertEquals([':p1' => 'imi', ':p2' => 'imi'], $query->getBinds());
        $this->assertEquals('score', $options->getScoreFieldName());
        $this->assertEquals('desc', $options->getOrderDirection());
    }
}
