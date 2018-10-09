<?php
namespace Imi\Db\Aop;

use Imi\Db\Parser\DbParser;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Db\Db;
use Imi\Model\ModelManager;

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
            
            try{
                // 开启事务
                $db->beginTransaction();
                $result = $joinPoint->proceed();
                // 提交事务
                $db->commit();
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
     * @return \Imi\Db\Interfaces\IDb
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