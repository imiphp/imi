<?php

declare(strict_types=1);

namespace Imi\Db\Event;

use Imi\Aop\AopManager;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\Bean;
use Imi\Db\Event\Param\DbExecuteEventParam;
use Imi\Db\Event\Param\DbPrepareEventParam;
use Imi\Event\Event;

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
            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'exec', [$this, 'aopExecute']);
            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'query', [$this, 'aopExecute']);
            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'batchExec', [$this, 'aopExecute']);

            AopManager::addAround('Imi\Db\*Drivers\*\Driver', 'prepare', [$this, 'aopPrepare']);
            AopManager::addAround('Imi\Db\*Drivers\*\Statement', 'execute', [$this, 'aopStatementExecute']);
        }
    }

    /**
     * @return mixed
     */
    public function aopExecute(AroundJoinPoint $joinPoint)
    {
        $beginTime = microtime(true);
        $result = $joinPoint->proceed();
        $endTime = microtime(true);
        [$sql] = $joinPoint->getArgs();
        $bindValues = [];
        $db = $joinPoint->getTarget();
        Event::trigger('IMI.DB.EXECUTE', [
            'db'         => $db,
            'sql'        => $sql,
            'beginTime'  => $beginTime,
            'endTime'    => $endTime,
            'time'       => $endTime - $beginTime,
            'bindValues' => $bindValues,
            'result'     => $result,
        ], $db, DbExecuteEventParam::class);

        return $result;
    }

    /**
     * @return mixed
     */
    public function aopPrepare(AroundJoinPoint $joinPoint)
    {
        $result = $joinPoint->proceed();
        [$sql] = $joinPoint->getArgs();
        Event::trigger('IMI.DB.PREPARE', [
            'statement' => $result,
            'sql'       => $sql,
        ], $joinPoint->getTarget(), DbPrepareEventParam::class);

        return $result;
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
        $beginTime = microtime(true);
        $result = $joinPoint->proceed();
        $endTime = microtime(true);
        $db = $statement->getDb();
        Event::trigger('IMI.DB.EXECUTE', [
            'db'         => $db,
            'statement'  => $statement,
            'sql'        => $sql,
            'beginTime'  => $beginTime,
            'endTime'    => $endTime,
            'time'       => $endTime - $beginTime,
            'bindValues' => $bindValues,
            'result'     => $result,
        ], $db, DbExecuteEventParam::class);

        return $result;
    }

    /**
     * 是否已启用.
     */
    public function isEnabled(): bool
    {
        return $this->enable;
    }
}
