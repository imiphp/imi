<?php

declare(strict_types=1);

namespace Imi\Async\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Async\Annotation\Async;
use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncResult;
use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionContainer;
use Imi\Bean\ReflectionUtil;

/**
 * @Aspect
 */
class AsyncAop
{
    /**
     * 异步执行.
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Async::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parseAsync(AroundJoinPoint $joinPoint)
    {
        $result = \Imi\Async\Async::exec(function () use ($joinPoint) {
            return $joinPoint->proceed();
        });
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $methodRef = ReflectionContainer::getMethodReflection($className, $joinPoint->getMethod());
        if (!$methodRef->hasReturnType() || ReflectionUtil::allowsType($methodRef->getReturnType(), IAsyncResult::class, $className))
        {
            return new AsyncResult($result);
        }
    }
}
