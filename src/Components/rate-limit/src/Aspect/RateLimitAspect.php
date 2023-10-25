<?php

declare(strict_types=1);

namespace Imi\RateLimit\Aspect;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\RateLimit\Annotation\BlockingConsumer;
use Imi\RateLimit\Annotation\RateLimit;
use Imi\RateLimit\RateLimiter;

#[Aspect]
class RateLimitAspect
{
    /**
     * 处理限流
     *
     * @return mixed
     */
    #[PointCut(type: 2, allow: ['Imi\\RateLimit\\Annotation\\RateLimit'])]
    #[Around]
    public function parse(AroundJoinPoint $joinPoint)
    {
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        $annotations = AnnotationManager::getMethodAnnotations($className, $method, [
            RateLimit::class,
            BlockingConsumer::class,
        ], true, true);
        /** @var RateLimit|null $rateLimit */
        $rateLimit = $annotations[RateLimit::class];
        /** @var BlockingConsumer|null $blockingConsumer */
        $blockingConsumer = $annotations[BlockingConsumer::class];
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
