<?php

declare(strict_types=1);

namespace Imi\RateLimit\Aspect;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\RateLimit\Annotation\BlockingConsumer;
use Imi\RateLimit\Annotation\WorkerLimit;
use Imi\RateLimit\WorkerLimiter;

/**
 * @Aspect
 */
class WorkerLimitAspect
{
    /**
     * 处理工作限流
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             WorkerLimit::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parse(AroundJoinPoint $joinPoint)
    {
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        /** @var WorkerLimit|null $workerLimit */
        $workerLimit = AnnotationManager::getMethodAnnotations($className, $method, WorkerLimit::class)[0] ?? null;
        /** @var BlockingConsumer|null $blockingConsumer */
        $blockingConsumer = AnnotationManager::getMethodAnnotations($className, $method, BlockingConsumer::class)[0] ?? null;
        if (null === $blockingConsumer)
        {
            $result = WorkerLimiter::call(function () use ($joinPoint) {
                return $joinPoint->proceed();
            }, $workerLimit->name, $workerLimit->max, $workerLimit->timeout, $workerLimit->callback, $workerLimit->poolName);
        }
        else
        {
            $result = WorkerLimiter::callBlock(function () use ($joinPoint) {
                return $joinPoint->proceed();
            }, $workerLimit->name, $workerLimit->max, $workerLimit->timeout, $blockingConsumer->timeout, $workerLimit->callback, $workerLimit->poolName);
        }

        return $result;
    }
}
