<?php

declare(strict_types=1);

namespace Imi\Db\Event;

use Imi\Aop\AopManager;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\Bean;
use Imi\Db\Event\Param\DbExecuteEventParam;
use Imi\Db\Event\Param\DbPrepareEventParam;
use Imi\Event\Event;
use Imi\Util\DelayBeanCallable;

/**
 * @Bean("DbQueryLog")
 */
class DbQueryLog
{
    /**
     * 是否已启用.
     */
    protected bool $enable = false;

    public function __init(): void
    {
        if ($this->enable)
        {
            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'exec', new DelayBeanCallable('DbQueryLog', 'aopExecute'));
            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'query', new DelayBeanCallable('DbQueryLog', 'aopExecute'));
            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'batchExec', new DelayBeanCallable('DbQueryLog', 'aopExecute'));

            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'prepare', new DelayBeanCallable('DbQueryLog', 'aopPrepare'));
            AopManager::addAround('Imi\Db\*Drivers\*\Statement', 'execute', new DelayBeanCallable('DbQueryLog', 'aopStatementExecute'));
        }
    }

    /**
     * @return mixed
     */
    public function aopExecute(AroundJoinPoint $joinPoint)
    {
        [$sql] = $joinPoint->getArgs();
        $bindValues = [];
        $db = $joinPoint->getTarget();
        $beginTime = microtime(true);
        try
        {
            return $joinPoint->proceed();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            $endTime = microtime(true);
            Event::trigger('IMI.DB.EXECUTE', [
                'db'         => $db,
                'sql'        => $sql,
                'beginTime'  => $beginTime,
                'endTime'    => $endTime,
                'time'       => $endTime - $beginTime,
                'bindValues' => $bindValues,
                'result'     => $result ?? null,
                'throwable'  => $th ?? null,
            ], $db, DbExecuteEventParam::class);
        }
    }

    /**
     * @return mixed
     */
    public function aopPrepare(AroundJoinPoint $joinPoint)
    {
        [$sql] = $joinPoint->getArgs();
        try
        {
            return $joinPoint->proceed();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            Event::trigger('IMI.DB.PREPARE', [
                'statement'  => $result ?? null,
                'sql'        => $sql,
                'throwable'  => $th ?? null,
            ], $joinPoint->getTarget(), DbPrepareEventParam::class);
        }
    }

    /**
     * @return mixed
     */
    public function aopStatementExecute(AroundJoinPoint $joinPoint)
    {
        /** @var \Imi\Db\Interfaces\IStatement $statement */
        $statement = $joinPoint->getTarget();
        $sql = $statement->getSql();
        $args = $joinPoint->getArgs();
        $bindValues = $args[0] ?? null;
        $db = $statement->getDb();
        $beginTime = microtime(true);
        try
        {
            return $joinPoint->proceed();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            $endTime = microtime(true);
            Event::trigger('IMI.DB.EXECUTE', [
                'db'         => $db,
                'statement'  => $statement,
                'sql'        => $sql,
                'beginTime'  => $beginTime,
                'endTime'    => $endTime,
                'time'       => $endTime - $beginTime,
                'bindValues' => $bindValues,
                'result'     => $result ?? null,
                'throwable'  => $th ?? null,
            ], $db, DbExecuteEventParam::class);
        }
    }

    /**
     * 是否已启用.
     */
    public function isEnabled(): bool
    {
        return $this->enable;
    }
}
