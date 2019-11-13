<?php
namespace Imi\Pool\Aop;

use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\BeanFactory;
use Imi\Pool\PoolManager;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Pool\Annotation\PoolClean;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @Aspect
 */
class PoolCleanAop
{
    /**
     * 过滤方法参数
     * @PointCut(
     *      type=PointCutType::ANNOTATION,
     *      allow={
     *          \Imi\Pool\Annotation\PoolClean::class
     *      }
     * )
     * @Before
     * @return mixed
     */
    public function parse(JoinPoint $joinPoint)
    {
        /** @var \Imi\Pool\Annotation\PoolClean $poolClean */
        $poolClean = AnnotationManager::getMethodAnnotations(BeanFactory::getObjectClass($joinPoint->getTarget()), $joinPoint->getMethod(), PoolClean::class)[0];
        switch($poolClean->mode)
        {
            case 'allow':
                foreach(PoolManager::getNames() as $poolName)
                {
                    if(!in_array($poolName, $poolClean->list))
                    {
                        PoolManager::getInstance($poolName)->close();
                    }
                }
                break;
            case 'deny':
                foreach(PoolManager::getNames() as $poolName)
                {
                    if(in_array($poolName, $poolClean->list))
                    {
                        PoolManager::getInstance($poolName)->close();
                    }
                }
                break;
        }
    }

}
