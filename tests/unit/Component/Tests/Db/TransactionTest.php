<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db;

use Imi\App;
use Imi\Db\Db;
use Imi\Test\BaseTest;
use Imi\Test\Component\Db\Classes\TestTransaction;

class TransactionTest extends BaseTest
{
    private function getObject(): TestTransaction
    {
        return App::getBean(TestTransaction::class);
    }

    public function testNestingCommit(): void
    {
        // 不在事务中，开启事务并提交事务
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        try
        {
            $object->nestingCommit();
            $this->assertEquals(0, $transaction->getTransactionLevels());
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }

        // 在事务中，不自动提交事务
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());
        try
        {
            $object->nestingCommit();
            // 事务嵌套
            $this->assertEquals(2, $transaction->getTransactionLevels());
            $db->commit();
            $this->assertEquals(0, $transaction->getTransactionLevels());
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testNestingRollback(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());

        try
        {
            $catched = false;
            try
            {
                $object->nestingRollback();
            }
            catch (\Throwable $th)
            {
                $catched = true;
                $this->assertEquals('gg', $th->getMessage());
                $this->assertEquals(0, $transaction->getTransactionLevels());
            }
            finally
            {
                if (!$catched)
                {
                    $this->assertTrue(false);
                }
            }
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testRequirementCommit1(): void
    {
        $this->expectExceptionMessageMatches('/.+::.+ can not run without transactional/');
        $object = $this->getObject();
        $object->requirementCommit();
    }

    public function testRequirementCommit2(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());
        try
        {
            $object->requirementCommit();
            // 事务嵌套
            $this->assertEquals(1, $transaction->getTransactionLevels());
            $db->commit();
            $this->assertEquals(0, $transaction->getTransactionLevels());
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testRequirementRollback(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());

        try
        {
            $catched = false;
            try
            {
                $object->requirementRollback();
            }
            catch (\Throwable $th)
            {
                $catched = true;
                $this->assertEquals('gg', $th->getMessage());
                $this->assertEquals(0, $transaction->getTransactionLevels());
            }
            finally
            {
                if (!$catched)
                {
                    $this->assertTrue(false);
                }
            }
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testAutoCommit(): void
    {
        // 不在事务中，开启事务并提交事务
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        try
        {
            $object->autoCommit();
            $this->assertEquals(0, $transaction->getTransactionLevels());
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }

        // 在事务中，不自动提交事务
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());
        try
        {
            $object->autoCommit();
            // 事务嵌套
            $this->assertEquals(1, $transaction->getTransactionLevels());
            $db->commit();
            $this->assertEquals(0, $transaction->getTransactionLevels());
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testAutoRollback(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();

        try
        {
            $catched = false;
            try
            {
                $object->autoRollback();
            }
            catch (\Throwable $th)
            {
                $catched = true;
                $this->assertEquals('gg', $th->getMessage());
                $this->assertEquals(0, $transaction->getTransactionLevels());
            }
            finally
            {
                if (!$catched)
                {
                    $this->assertTrue(false);
                }
            }
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testRollbackPart1(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());

        try
        {
            $catched = false;
            try
            {
                $object->rollbackPart1();
            }
            catch (\Throwable $th)
            {
                $catched = true;
                $this->assertEquals('gg', $th->getMessage());
                $this->assertEquals(1, $transaction->getTransactionLevels());
            }
            finally
            {
                if (!$catched)
                {
                    $this->assertTrue(false);
                }
            }
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }

    public function testRollbackPartAll(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $db->beginTransaction();
        $this->assertEquals(1, $transaction->getTransactionLevels());

        try
        {
            $catched = false;
            try
            {
                $object->rollbackPartAll();
            }
            catch (\Throwable $th)
            {
                $catched = true;
                $this->assertEquals('gg', $th->getMessage());
                $this->assertEquals(0, $transaction->getTransactionLevels());
            }
            finally
            {
                if (!$catched)
                {
                    $this->assertTrue(false);
                }
            }
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
    }
}
