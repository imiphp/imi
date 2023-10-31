<?php

declare(strict_types=1);

namespace Imi\Async\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Async\Contract\IAsyncResult;
use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionContainer;
use Imi\Bean\ReflectionUtil;

#[Aspect]
class AsyncAop
{
    /**
     * 异步执行.
     *
     * @return mixed
     */
    #[PointCut(type: \Imi\Aop\PointCutType::ANNOTATION, allow: [\Imi\Async\Annotation\Async::class])]
    #[Around]
    public function parseAsync(AroundJoinPoint $joinPoint)
    {
        $result = \Imi\Async\Async::exec(static fn () => $joinPoint->proceed());
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $methodRef = ReflectionContainer::getMethodReflection($className, $joinPoint->getMethod());
        if (!$methodRef->hasReturnType() || ReflectionUtil::allowsType($methodRef->getReturnType(), IAsyncResult::class, $className))
        {
            return $result;
        }
    }

    /**
     * 延后执行.
     *
     * @return mixed
     */
    #[PointCut(type: \Imi\Aop\PointCutType::ANNOTATION, allow: [\Imi\Async\Annotation\Defer::class])]
    #[Around]
    public function parseDefer(AroundJoinPoint $joinPoint)
    {
        $result = \Imi\Async\Async::defer(static fn () => $joinPoint->proceed());
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $methodRef = ReflectionContainer::getMethodReflection($className, $joinPoint->getMethod());
        if (!$methodRef->hasReturnType() || ReflectionUtil::allowsType($methodRef->getReturnType(), IAsyncResult::class, $className))
        {
            return $result;
        }
    }

    /**
     * 延后执行.
     *
     * @return mixed
     */
    #[PointCut(type: \Imi\Aop\PointCutType::ANNOTATION, allow: [\Imi\Async\Annotation\DeferAsync::class])]
    #[Around]
    public function parseDeferAsync(AroundJoinPoint $joinPoint)
    {
        $result = \Imi\Async\Async::deferAsync(static fn () => $joinPoint->proceed());
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $methodRef = ReflectionContainer::getMethodReflection($className, $joinPoint->getMethod());
        if (!$methodRef->hasReturnType() || ReflectionUtil::allowsType($methodRef->getReturnType(), IAsyncResult::class, $className))
        {
            return $result;
        }
    }
}
