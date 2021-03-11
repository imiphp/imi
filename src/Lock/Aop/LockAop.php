<?php

namespace Imi\Lock\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Lock\Annotation\Lockable;

/**
 * @Aspect
 */
class LockAop
{
    use TLockableParser;

    /**
     * 处理方法加锁
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Lock\Annotation\Lockable::class,
     *         }
     * )
     * @Around
     *
     * @param AroundJoinPoint $joinPoint
     *
     * @return mixed
     */
    public function parseLock(AroundJoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $method = $joinPoint->getMethod();
        $class = get_parent_class($target);
        if (false === $class)
        {
            return $joinPoint->proceed();
        }
        /** @var Lockable|null $lockable */
        $lockable = AnnotationManager::getMethodAnnotations($class, $method, Lockable::class)[0] ?? null;
        if (null === $lockable)
        {
            return $joinPoint->proceed();
        }

        return $this->parseLockable($target, $method, $joinPoint->getArgs(), $lockable, function () use ($joinPoint) {
            return $joinPoint->proceed();
        });
    }
}
