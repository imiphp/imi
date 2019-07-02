<?php
namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\App;
use Imi\Db\Db;
use PHPUnit\Framework\Assert;

/**
 * @testdox Db
 */
class DbTest extends BaseTest
{
    public function testInject()
    {
        $test = App::getBean('TestInjectDb');
        $test->test();
    }

    public function testExec()
    {
        $db = Db::getInstance();
        $sql = "insert into tb_article(title,content,time)values('title', 'content', '2019-06-21')";
        $result = $db->exec($sql);
        Assert::assertEquals(1, $result);

        Assert::assertEquals($sql, $db->lastSql());
    }

    public function testQuery()
    {
        $db = Db::getInstance();
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
        $db = Db::getInstance();
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
        $db = Db::getInstance();
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
        $db = Db::getInstance();
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
        $db = Db::getInstance();
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

}
