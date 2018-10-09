<?php
namespace Imi\Db\Aop;

use Imi\Db\Parser\DbParser;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Db\Db;
use Imi\Model\ModelManager;
use Imi\Db\Annotation\TransactionType;
use Imi\Bean\BeanFactory;

/**
 * @Aspect
 */
class TransactionAop
{
    /**
     * 自动事务支持
     * @PointCut(
     *         allow={
     *             "*::*",
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function defer(AroundJoinPoint $joinPoint)
    {
        $transaction = DbParser::getInstance()->getMethodTransaction(get_parent_class($joinPoint->getTarget()), $joinPoint->getMethod());
        if(null === $transaction)
        {
            return $joinPoint->proceed();
        }
        else
        {
            $object = $joinPoint->getTarget();
            $db = $this->getDb($transaction, $object);
            if(!$db)
            {
                throw new \RuntimeException('@Transaction failed, get db failed');
            }
            try{
                switch($transaction->type)
                {
                    case TransactionType::NESTING:
                        // 开启事务
                        $db->beginTransaction();
                        break;
                    case TransactionType::REQUIREMENT:
                        if(!$db->inTransaction())
                        {
                            throw new \RuntimeException(sprintf('%s::%s can not run without transactional', BeanFactory::getObjectClass($object), $joinPoint->getMethod()));
                        }
                        break;
                    case TransactionType::AUTO:
                        if(!$db->inTransaction())
                        {
                            // 开启事务
                            $db->beginTransaction();
                        }
                        break;
                }
                $result = $joinPoint->proceed();
                if($transaction->autoCommit)
                {
                    // 提交事务
                    $db->commit();
                }
                return $result;
            }
            catch(\Throwable $ex)
            {
                // 回滚事务
                if($db->inTransaction())
                {
                    $db->rollBack();
                }
                throw $ex;
            }
        }
    }

    /**
     * 获取数据库连接
     *
     * @param \Imi\Db\Annotation\Transaction $transaction
     * @param object $object
     * @return \Imi\Db\Interfaces\IDb|null
     */
    private function getDb($transaction, $object)
    {
        if($object instanceof \Imi\Model\Model)
        {
            $db = Db::getInstance(ModelManager::getDbPoolName($object));
        }
        else
        {
            $db = Db::getInstance($transaction->dbPoolName);
        }
        return $db;
    }
}