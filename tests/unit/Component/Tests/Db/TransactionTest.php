<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db;

use Imi\App;
use Imi\Db\Db;
use Imi\Db\Transaction\Transaction;
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
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);

        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        // 在事务中，不自动提交事务
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    public function testNestingCommit2(): void
    {
        // 不在事务中，开启事务并提交事务
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        try
        {
            $object->nestingCommit2();
            $this->assertEquals(0, $transaction->getTransactionLevels());
        }
        finally
        {
            if ($db->inTransaction())
            {
                $transaction->rollBack();
            }
        }
        $this->assertEmptyEvents($transaction);

        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        // 在事务中，不自动提交事务
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $this->assertEquals(1, $transaction->getTransactionLevels());
        try
        {
            $object->nestingCommit2();
            // 事务嵌套
            $this->assertEquals(3, $transaction->getTransactionLevels());
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
        $this->assertEmptyEvents($transaction);
    }

    public function testNestingRollback(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    public function testNestingRollback2(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $this->assertEquals(1, $transaction->getTransactionLevels());

        try
        {
            $catched = false;
            try
            {
                $object->nestingRollback2();
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
        $this->assertEmptyEvents($transaction);
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
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    public function testRequirementRollback(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    public function testAutoCommit(): void
    {
        // 不在事务中，开启事务并提交事务
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);

        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        // 在事务中，不自动提交事务
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    public function testAutoRollback(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });

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
        $this->assertEmptyEvents($transaction);
    }

    public function testRollbackPart1(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    public function testRollbackPartAll(): void
    {
        $object = $this->getObject();
        $db = Db::getInstance();
        $transaction = $db->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
        $db->beginTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
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
        $this->assertEmptyEvents($transaction);
    }

    private function assertEmptyEvents(Transaction $event): void
    {
        foreach (['__events', '__eventQueue', '__eventChangeRecords', '__sortedEventQueue'] as $property)
        {
            $propertyRef = new \ReflectionProperty($event, $property);
            $propertyRef->setAccessible(true);
            $events = $propertyRef->getValue($event);

            $this->assertEquals([], $events);
        }
    }
}
