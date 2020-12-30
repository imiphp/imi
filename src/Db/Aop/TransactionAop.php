<?php

declare(strict_types=1);

namespace Imi\Db\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Db\Annotation\RollbackType;
use Imi\Db\Annotation\Transaction;
use Imi\Db\Annotation\TransactionType;
use Imi\Db\Db;
use Imi\Db\Interfaces\IDb;

/**
 * @Aspect
 */
class TransactionAop
{
    /**
     * 自动事务支持
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Transaction::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parseTransaction(AroundJoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $method = $joinPoint->getMethod();
        $transaction = AnnotationManager::getMethodAnnotations(get_parent_class($target), $method, Transaction::class)[0] ?? null;
        if (null === $transaction)
        {
            return $joinPoint->proceed();
        }
        else
        {
            $db = $this->getDb($transaction, $target);
            if (!$db)
            {
                throw new \RuntimeException('@Transaction failed, get db failed');
            }
            try
            {
                $isBeginTransaction = !$db->inTransaction();
                $transactionType = $transaction->type;
                switch ($transactionType)
                {
                    case TransactionType::NESTING:
                        // 开启事务
                        $db->beginTransaction();
                        break;
                    case TransactionType::REQUIREMENT:
                        if (!$db->inTransaction())
                        {
                            throw new \RuntimeException(sprintf('%s::%s can not run without transactional', BeanFactory::getObjectClass($target), $method));
                        }
                        break;
                    case TransactionType::AUTO:
                        if ($isBeginTransaction)
                        {
                            // 开启事务
                            $db->beginTransaction();
                        }
                        break;
                }
                $result = $joinPoint->proceed();
                if ($isBeginTransaction && $transaction->autoCommit && (TransactionType::NESTING === $transactionType || TransactionType::AUTO === $transactionType))
                {
                    // 提交事务
                    $db->commit();
                }

                return $result;
            }
            catch (\Throwable $ex)
            {
                // 回滚事务
                if ($db->inTransaction())
                {
                    switch ($transaction->rollbackType)
                    {
                        case RollbackType::ALL:
                            $db->rollBack();
                            break;
                        case RollbackType::PART:
                            $db->rollBack($transaction->rollbackLevels);
                            break;
                    }
                }
                throw $ex;
            }
        }
    }

    /**
     * 获取数据库连接.
     *
     * @param \Imi\Db\Annotation\Transaction $transaction
     * @param object                         $object
     *
     * @return \Imi\Db\Interfaces\IDb|null
     */
    private function getDb(Transaction $transaction, object $object): ?IDb
    {
        if ($object instanceof \Imi\Model\Model)
        {
            $db = Db::getInstance($object->__getMeta()->getDbPoolName());
        }
        else
        {
            $db = Db::getInstance($transaction->dbPoolName);
        }

        return $db;
    }
}
