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

#[Bean(name: 'DbQueryLog')]
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

    public function aopExecute(AroundJoinPoint $joinPoint): mixed
    {
        [$sql] = $joinPoint->getArgs();
        $bindValues = [];
        $db = $joinPoint->getTarget();
        $beginTime = microtime(true);
        try
        {
            return $result = $joinPoint->proceed();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            $endTime = microtime(true);
            Event::dispatch(new DbExecuteEventParam($db, null, $sql, $beginTime, $endTime, $endTime - $beginTime, $bindValues, $result ?? null, $th ?? null));
        }
    }

    public function aopPrepare(AroundJoinPoint $joinPoint): mixed
    {
        [$sql] = $joinPoint->getArgs();
        try
        {
            return $result = $joinPoint->proceed();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            Event::dispatch(new DbPrepareEventParam($joinPoint->getTarget(), $result ?? null, $sql, $th ?? null));
        }
    }

    public function aopStatementExecute(AroundJoinPoint $joinPoint): mixed
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
            return $result = $joinPoint->proceed();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            $endTime = microtime(true);
            Event::dispatch(new DbExecuteEventParam($db, $statement, $sql, $beginTime, $endTime, $endTime - $beginTime, $bindValues, $result ?? null, $th ?? null));
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
