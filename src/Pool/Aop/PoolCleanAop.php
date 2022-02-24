<?php

declare(strict_types=1);

namespace Imi\Pool\Aop;

use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Pool\Annotation\PoolClean;
use Imi\Pool\PoolManager;

/**
 * @Aspect
 */
class PoolCleanAop
{
    /**
     * 过滤方法参数.
     *
     * @PointCut(
     *      type=PointCutType::ANNOTATION,
     *      allow={
     *          \Imi\Pool\Annotation\PoolClean::class
     *      }
     * )
     * @Before
     *
     * @return mixed
     */
    public function parse(JoinPoint $joinPoint)
    {
        /** @var \Imi\Pool\Annotation\PoolClean $poolClean */
        $poolClean = AnnotationManager::getMethodAnnotations(BeanFactory::getObjectClass($joinPoint->getTarget()), $joinPoint->getMethod(), PoolClean::class, true, true);
        switch ($poolClean->mode)
        {
            case 'allow':
                PoolManager::cleanAllow($poolClean->list);
                break;
            case 'deny':
                PoolManager::cleanDeny($poolClean->list);
                break;
        }
    }
}
