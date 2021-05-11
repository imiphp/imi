<?php

namespace Imi\RateLimit\Aspect;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\RateLimit\Annotation\BlockingConsumer;
use Imi\RateLimit\Annotation\RateLimit;
use Imi\RateLimit\RateLimiter;

/**
 * @Aspect
 */
class RateLimitAspect
{
    /**
     * 处理限流
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             RateLimit::class
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
        /** @var RateLimit|null $rateLimit */
        $rateLimit = AnnotationManager::getMethodAnnotations($className, $method, RateLimit::class)[0] ?? null;
        /** @var BlockingConsumer|null $blockingConsumer */
        $blockingConsumer = AnnotationManager::getMethodAnnotations($className, $method, BlockingConsumer::class)[0] ?? null;
        if (null === $blockingConsumer)
        {
            $result = RateLimiter::limit($rateLimit->name, $rateLimit->capacity, $rateLimit->callback, $rateLimit->fill, $rateLimit->unit, $rateLimit->deduct, $rateLimit->poolName);
        }
        else
        {
            $result = RateLimiter::limitBlock($rateLimit->name, $rateLimit->capacity, $rateLimit->callback, $blockingConsumer->timeout, $rateLimit->fill, $rateLimit->unit, $rateLimit->deduct, $rateLimit->poolName);
        }
        if (true === $result)
        {
            return $joinPoint->proceed();
        }
        else
        {
            return $result;
        }
    }
}
