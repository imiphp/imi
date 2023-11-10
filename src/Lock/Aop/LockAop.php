<?php

declare(strict_types=1);

namespace Imi\Lock\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Lock\Annotation\Lockable;

#[Aspect]
class LockAop
{
    use TLockableParser;

    /**
     * 处理方法加锁
     */
    #[PointCut(type: \Imi\Aop\PointCutType::ANNOTATION, allow: [\Imi\Lock\Annotation\Lockable::class])]
    #[Around]
    public function parseLock(AroundJoinPoint $joinPoint): mixed
    {
        $target = $joinPoint->getTarget();
        $method = $joinPoint->getMethod();
        $class = BeanFactory::getObjectClass($target);
        /** @var Lockable|null $lockable */
        $lockable = AnnotationManager::getMethodAnnotations($class, $method, Lockable::class, true, true);

        return $this->parseLockable($target, $method, $joinPoint->getArgs(), $lockable, static fn () => $joinPoint->proceed());
    }
}
