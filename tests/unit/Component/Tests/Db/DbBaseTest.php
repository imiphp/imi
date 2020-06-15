<?php
namespace Imi\Test\Component\Tests\Db;

use Imi\App;
use Imi\Db\Db;
use Imi\Test\BaseTest;
use Imi\Db\Interfaces\IDb;
use PHPUnit\Framework\Assert;

/**
 * @testdox Db
 */
abstract class DbBaseTest extends BaseTest
{
    /**
     * 连接池名
     *
     * @var string
     */
    protected $poolName;

    public function testInject()
    {
        $test = App::getBean('TestInjectDb');
        $test->test();
    }

    public function testExec()
    {
        $db = Db::getInstance($this->poolName);
        $db->exec('TRUNCATE tb_article');
        $sql = "insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')";
        $result = $db->exec($sql);
        Assert::assertEquals(1, $result);

        Assert::assertEquals($sql, $db->lastSql());
    }

    public function testQuery()
    {
        $db = Db::getInstance($this->poolName);
        $stmt = $db->query('select * from tb_article where id = 1');
        Assert::assertInstanceOf(\Imi\Db\Interfaces\IStatement::class, $stmt);
        Assert::assertEquals([
            [
                'id'        =>  '1',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ]
        ], $stmt->fetchAll());
    }

    public function testPreparePositional()
    {
        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, 1);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        =>  '1',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ]
        ], $stmt->fetchAll());
    }

    public function testPrepareNamed()
    {
        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = :id');
        $stmt->bindValue(':id', 1);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([
            [
                'id'        =>  '1',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ]
        ], $stmt->fetchAll());
    }

    public function testTransactionCommit()
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
                'id'        =>  $id . '',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ]
        ], $stmt->fetchAll());
    }

    public function testTransactionRollback()
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

    public function testTransUseCommit()
    {
        $id = null;
        Db::transUse(function(IDb $db) use(&$id){
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
                'id'        =>  $id . '',
                'title'     =>  'title',
                'content'   =>  'content',
                'time'      =>  '2019-06-21 00:00:00',
            ]
        ], $stmt->fetchAll());
    }

    public function testTransUseRollback()
    {
        $id = null;
        try {
            Db::transUse(function(IDb $db) use(&$id){
                Assert::assertTrue($db->inTransaction());
                $result = $db->exec("insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')");
                Assert::assertEquals(1, $result);
                $id = $db->lastInsertId();
                throw new \RuntimeException('gg');
            }, $this->poolName);
        } catch(\Throwable $th) {
            Assert::assertEquals('gg', $th->getMessage());
        }

        $db = Db::getInstance($this->poolName);
        $stmt = $db->prepare('select * from tb_article where id = ?');
        $stmt->bindValue(1, $id);
        Assert::assertTrue($stmt->execute());
        Assert::assertEquals([], $stmt->fetchAll());
    }

    public function testTransactionRollbackRollbackEvent()
    {
        $db = Db::getInstance($this->poolName);
        $db->beginTransaction();
        Assert::assertTrue($db->inTransaction());
        $this->assertEquals(1, $db->getTransactionLevels());
        $r1 = false;
        $db->getTransaction()->onTransactionRollback(function() use(&$r1){
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

    public function testTransactionRollbackCommitEvent()
    {
        $db = Db::getInstance($this->poolName);
        $db->beginTransaction();
        Assert::assertTrue($db->inTransaction());
        $this->assertEquals(1, $db->getTransactionLevels());
        $r1 = false;
        $db->getTransaction()->onTransactionCommit(function() use(&$r1){
            $r1 = true;
        });
        $db->commit();
        $this->assertEquals(0, $db->getTransactionLevels());
        $this->assertFalse($db->inTransaction());
        $this->assertTrue($r1);
    }

}
