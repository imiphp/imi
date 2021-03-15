<?php

namespace Imi\Db\Event;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\AnnotationManager;
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
     *
     * @var bool
     */
    protected bool $enable = false;

    public function __init(): void
    {
        if ($this->enable)
        {
            // 类
            AnnotationManager::setClassAnnotations(static::class, new \Imi\Aop\Annotation\Aspect());

            // 方法
            $annotations = [];
            $annotations[] = new Around();
            $annotations[] = $pointCut = new PointCut();
            $pointCut->allow = [
                'Imi\Db\Drivers\*\Driver::exec',
                'Imi\Db\Drivers\*\Driver::query',
                'Imi\Db\Drivers\*\Driver::batchExec',
            ];
            AnnotationManager::setMethodAnnotations(static::class, 'aopExecute', ...$annotations);

            $annotations = [];
            $annotations[] = new Around();
            $annotations[] = $pointCut = new PointCut();
            $pointCut->allow = [
                'Imi\Db\Drivers\*\Driver::prepare',
            ];
            AnnotationManager::setMethodAnnotations(static::class, 'aopPrepare', ...$annotations);

            $annotations = [];
            $annotations[] = new Around();
            $annotations[] = $pointCut = new PointCut();
            $pointCut->allow = [
                'Imi\Db\Drivers\*\Statement::execute',
            ];
            AnnotationManager::setMethodAnnotations(static::class, 'aopStatementExecute', ...$annotations);
        }
    }

    /**
     * @param \Imi\Aop\AroundJoinPoint $joinPoint
     *
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
     * @param \Imi\Aop\AroundJoinPoint $joinPoint
     *
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
     * @param \Imi\Aop\AroundJoinPoint $joinPoint
     *
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
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enable;
    }
}
