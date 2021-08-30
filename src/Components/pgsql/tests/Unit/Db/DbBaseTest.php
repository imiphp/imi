<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db;

use Imi\Db\Db;
use Imi\Db\Interfaces\IDb;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Db
 */
abstract class DbBaseTest extends TestCase
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName;

    public function testExec(): void
    {
        $db = Db::getInstance($this->poolName);
        $db->exec('TRUNCATE tb_article RESTART IDENTITY');
        $sql = "insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')";
        $result = $db->exec($sql);
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

    public function testQuery(): void
    {
        $db = Db::getInstance($this->poolName);
        $stmt = $db->query('select * from tb_article where id = 1');
        Assert::assertInstanceOf(\Imi\Db\Interfaces\IStatement::class, $stmt);
        Assert::assertEquals([
            [
                'id'        => '1',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $stmt->fetchAll());
    }

    public function testPreparePositional(): void
    {
        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, 1);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => '1',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
            ],
        ], $stmt->fetchAll());
    }

    public function testPrepareNamed(): void
    {
        $db = Db::getInstance($this->poolName);

        // 有冒号
        $stmt = $db->prepare('select tb_article.*, :v as v from tb_article where id = :id');
        $stmt->bindValue(':id', 1);
        $stmt->bindValue(':v', 2);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => '1',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'v'         => 2,
            ],
        ], $stmt->fetchAll());

        // 无冒号
        $stmt = $db->prepare('select tb_article.*, :v as v from tb_article where id = :id');
        $stmt->bindValue('id', 1);
        $stmt->bindValue('v', 2);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        => '1',
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
                'v'         => 2,
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
                'id'        => $id,
                'title'     => 'title',
                'content'   => 'content',
                'time'      => '2019-06-21 00:00:00',
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
}
