<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db;

use function array_column;
use function array_column_ex;
use function date;
use Imi\App;
use Imi\Db\Db;
use Imi\Db\Interfaces\IDb;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;
use function time;

/**
 * @testdox Db
 */
abstract class DbBaseTest extends BaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected ?string $poolName;

    public function testInject(): void
    {
        /** @var \Imi\Test\Component\Db\Classes\TestInjectDb $test */
        $test = App::getBean('TestInjectDb');
        $test->test();
    }

    public function testExec(): void
    {
        $db = Db::getInstance($this->poolName);
        $db->exec('TRUNCATE tb_article');
        $sql = "insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')";
        $result = $db->exec($sql);
        Assert::assertEquals(1, $result);
        Assert::assertEquals($sql, $db->lastSql());

        Db::exec('TRUNCATE tb_article', [], $this->poolName);
        $sql = "insert into tb_article(title,content,time)values('title-2', 'content-2', '2021-08-20')";
        $result = Db::exec($sql, [], $this->poolName);
        Assert::assertEquals(1, $result);
        Assert::assertEquals($sql, $db->lastSql());
    }

    public function testBatchExec(): void
    {
        $db = Db::getInstance($this->poolName);
        $result = $db->batchExec('select 1 as a;update tb_article set id = 1 where id = 1;select 2 as b;');
        $this->assertEquals([
            [['a' => 1]],
            [],
            [['b' => 2]],
        ], $result);
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
        $id = (int) $result->getLastInsertId();
        $record = $query->from('tb_article')->where('id', '=', $id)->select()->get();
        Assert::assertEquals([
            'id'        => $id,
            'title'     => 'title',
            'content'   => 'content',
            'time'      => '2019-06-21 00:00:00',
            'member_id' => 0,
        ], $record);

        return [
            'id' => $id,
        ];
    }

    /**
     * @depends testInsert
     */
    public function testQuery(array $args): void
    {
        ['id' => $id] = $args;
        $db = Db::getInstance($this->poolName);
        $stmt = $db->query('select * from tb_article where id = ' . $id);
        Assert::assertInstanceOf(\Imi\Db\Interfaces\IStatement::class, $stmt);
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());
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
            'member_id' => 0,
        ], $result);
    }

    /**
     * @depends testInsert
     */
    public function testPreparePositional(array $args): void
    {
        ['id' => $id] = $args;
        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, $id);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());

        $stmt = $db->prepare('select * from tb_article where id = ?');
        Assert::assertTrue($stmt->execute([$id]));
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());

        $stmt = $db->prepare('select ? as a, ? as b, ? as c');
        Assert::assertTrue($stmt->execute([1, 2, 3]));
        Assert::assertEquals([
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
        ], $stmt->fetchAll());
    }

    /**
     * @depends testInsert
     */
    public function testPrepareNamed(array $args): void
    {
        ['id' => $id] = $args;
        $db = Db::getInstance($this->poolName);

        // 有冒号
        $stmt = $db->prepare('select tb_article.*, :v as v from tb_article where id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':v', 2);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'v'         => 2,
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());

        // 无冒号
        $stmt = $db->prepare('select tb_article.*, :v as v from tb_article where id = :id');
        $stmt->bindValue('id', $id);
        $stmt->bindValue('v', 2);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'v'         => 2,
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());

        // execute
        $stmt = $db->prepare('select tb_article.*, :v as v from tb_article where id = :id');
        Assert::assertTrue($stmt->execute([
            'id' => $id,
            ':v' => 2,
        ]));
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'v'         => 2,
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());
    }

    public function testTransactionCommit(): void
    {
        $db = Db::getInstance($this->poolName);
        $db->beginTransaction();
        Assert::assertTrue($db->inTransaction());

        $result = $db->exec("insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')");
        Assert::assertEquals(1, $result);
        $id = $db->lastInsertId();
        $db->commit();
        Assert::assertNotTrue($db->inTransaction());

        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, $id);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => $id . '',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());
    }

    public function testTransactionRollback(): void
    {
        $db = Db::getInstance($this->poolName);
        $db->beginTransaction();
        Assert::assertTrue($db->inTransaction());

        $result = $db->exec("insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')");
        Assert::assertEquals(1, $result);
        $id = $db->lastInsertId();
        $db->rollBack();
        Assert::assertNotTrue($db->inTransaction());

        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, $id);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([], $stmt->fetchAll());
    }

    public function testTransUseCommit(): void
    {
        $id = null;
        Db::transUse(function (IDb $db) use (&$id) {
            Assert::assertTrue($db->inTransaction());
            $result = $db->exec("insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')");
            Assert::assertEquals(1, $result);
            $id = $db->lastInsertId();
        }, $this->poolName);

        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, $id);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => $id . '',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());
    }

    public function testTransUseRollback(): void
    {
        $id = null;
        try
        {
            Db::transUse(function (IDb $db) use (&$id) {
                Assert::assertTrue($db->inTransaction());
                $result = $db->exec("insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')");
                Assert::assertEquals(1, $result);
                $id = $db->lastInsertId();
                throw new \RuntimeException('gg');
            }, $this->poolName);
        }
        catch (\Throwable $th)
        {
            Assert::assertEquals('gg', $th->getMessage());
        }

        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, $id);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([], $stmt->fetchAll());
    }

    public function testTransactionRollbackRollbackEvent(): void
    {
        $db = Db::getInstance($this->poolName);
        $db->beginTransaction();
        Assert::assertTrue($db->inTransaction());
        $this->assertEquals(1, $db->getTransactionLevels());
        $r1 = false;
        $db->getTransaction()->onTransactionRollback(function () use (&$r1) {
            $r1 = true;
        });

        $result = $db->exec("insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')");
        Assert::assertEquals(1, $result);
        $id = $db->lastInsertId();
        $db->rollBack();
        $this->assertEquals(0, $db->getTransactionLevels());
        Assert::assertNotTrue($db->inTransaction());

        $this->assertTrue($r1);
    }

    public function testTransactionRollbackCommitEvent(): void
    {
        $db = Db::getInstance($this->poolName);
        $db->beginTransaction();
        Assert::assertTrue($db->inTransaction());
        $this->assertEquals(1, $db->getTransactionLevels());
        $r1 = false;
        $db->getTransaction()->onTransactionCommit(function () use (&$r1) {
            $r1 = true;
        });
        $db->commit();
        $this->assertEquals(0, $db->getTransactionLevels());
        $this->assertFalse($db->inTransaction());
        $this->assertTrue($r1);
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
                'member_id' => 0,
            ],
        ], $result->getArray());

        $result = Db::select('select * from tb_article where id = ?', [$id], $this->poolName);
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $result->getArray());
    }

    public function testBatchInsert(): array
    {
        $query = Db::query($this->poolName);

        $basicRowCount = $query->table('tb_article')->count();

        $insertCount = 100;
        $data = [];

        $time = time();
        for ($i = 1; $i <= $insertCount; ++$i)
        {
            $data["k_{$i}"] = [
                'title'     => "title_{$i}",
                'content'   => "content_{$i}",
                'time'      => date('Y-m-d H:i:s', $time + $i),
                'member_id' => $i,
            ];
        }
        $query->table('tb_article')->batchInsert($data);

        $newRowCount = $query->table('tb_article')->count();

        $this->assertEquals($basicRowCount + $insertCount, $newRowCount);

        $items = $query->table('tb_article')->select()->getArray();

        return [
            'origin' => $items,
        ];
    }

    /**
     * @depends testBatchInsert
     */
    public function testCursor(array $args): void
    {
        $query = Db::query($this->poolName);

        $data = [];
        foreach ($query->table('tb_article')->cursor() as $item)
        {
            $data[] = $item;
        }

        $this->assertEquals($args['origin'], $data);

        // 空返回
        $data = [];
        foreach ($query->table('tb_article')->where('member_id', '=', -1)->cursor() as $item)
        {
            $data[] = $item;
        }

        $this->assertEmpty($data);
    }

    /**
     * @depends testBatchInsert
     */
    public function testChunk(array $args): void
    {
        $query = Db::query($this->poolName);

        $data = [];
        foreach ($query->table('tb_article')->chunkById(36, 'id') as $items)
        {
            foreach ($items->getArray() as $item)
            {
                $data[] = $item;
            }
        }

        $this->assertEquals($args['origin'], $data);

        // 自动重置排序
        $data = [];
        foreach ($query->table('tb_article')->order('id', 'desc')->chunkById(36, 'id') as $items)
        {
            foreach ($items->getArray() as $item)
            {
                $data[] = $item;
            }
        }

        $this->assertEquals($args['origin'], $data);

        // 空返回
        $data = [];
        foreach ($query->table('tb_article')->where('member_id', '=', -1)->chunkById(36, 'id') as $items)
        {
            foreach ($items->getArray() as $item)
            {
                $data[] = $item;
            }
        }

        $this->assertEmpty($data);
    }

    /**
     * @depends testBatchInsert
     */
    public function testChunkEach(array $args): void
    {
        $query = Db::query($this->poolName);

        $data = [];
        foreach ($query->table('tb_article')->chunkEach(36, 'id') as $item)
        {
            $data[] = $item;
        }

        $this->assertEquals($args['origin'], $data);

        // 自动重置排序
        $data = [];
        foreach ($query->table('tb_article')->order('id', 'desc')->chunkEach(36, 'id') as $item)
        {
            $data[] = $item;
        }

        $this->assertEquals($args['origin'], $data);

        // 空返回
        $data = [];
        foreach ($query->table('tb_article')->where('member_id', '=', -1)->chunkEach(36, 'id') as $item)
        {
            $data[] = $item;
        }

        $this->assertEmpty($data);
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

    /**
     * @depends testBatchInsert
     */
    public function testColumn(array $args): void
    {
        $origin = $args['origin'];

        $data = Db::query($this->poolName)
            ->table('tb_article')
            ->column('content');

        $this->assertEquals(array_column($origin, 'content'), $data);

        $data = Db::query($this->poolName)
            ->table('tb_article')
            ->column('content', 'id');

        $this->assertEquals(array_column($origin, 'content', 'id'), $data);

        $data = Db::query($this->poolName)
            ->table('tb_article')
            ->column(['id', 'content'], 'id');

        $this->assertEquals(array_column_ex($origin, ['id', 'content'], 'id'), $data);

        $data = Db::query($this->poolName)
            ->table('tb_article')
            ->column(['title', 'content', 'time'], 'id');

        $this->assertEquals(array_column_ex($origin, ['title', 'content', 'time', 'id'], 'id'), $data);
    }

    /**
     * @depends testInsert
     */
    public function testPrepare(array $args): void
    {
        ['id' => $id] = $args;
        $stmt = Db::prepare('select * from tb_article where id = ' . $id, $this->poolName);
        $this->assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());

        $stmt = Db::prepare('select * from tb_article where id = ?', $this->poolName);
        $this->assertTrue($stmt->execute([$id]));
        Assert::assertEquals([
            [
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'member_id' => 0,
            ],
        ], $stmt->fetchAll());
    }

    public function testWhereInEmptyValue(): void
    {
        $sql = Db::query()->table('test1')
            ->whereIn('a1', [1, 2, 3])
            ->whereIn('a2', [])
            ->whereNotIn('a3', [1, 2, 3])
            ->whereNotIn('a4', [])
            ->buildSelectSql();

        $this->assertEquals(
            'select * from `test1` where `a1` in (:p1,:p2,:p3) and `a2` in (0 = 1) and `a3` not in (:p4,:p5,:p6) and `a4` not in (1 = 1)',
            $sql
        );
    }
}
