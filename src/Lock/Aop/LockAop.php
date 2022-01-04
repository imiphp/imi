<?php

declare(strict_types=1);

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
     * @return mixed
     */
    public function parseLock(AroundJoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $method = $joinPoint->getMethod();
        $class = get_parent_class($target);
        /** @var Lockable|null $lockable */
        $lockable = AnnotationManager::getMethodAnnotations($class, $method, Lockable::class)[0] ?? null;

        return $this->parseLockable($target, $method, $joinPoint->getArgs(), $lockable, fn() => $joinPoint->proceed());
    }
}
