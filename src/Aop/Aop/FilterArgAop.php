<?php

namespace Imi\Aop\Aop;

use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\FilterArg;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Util\ClassObject;

/**
 * @Aspect
 */
class FilterArgAop
{
    /**
     * 过滤方法参数.
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             FilterArg::class
     *         }
     * )
     * @Before
     *
     * @return mixed
     */
    public function parse(JoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        $filterArgs = AnnotationManager::getMethodAnnotations($class, $method, FilterArg::class);
        $args = ClassObject::convertArgsToKV($class, $method, $joinPoint->getArgs());

        foreach ($filterArgs as $filterArg)
        {
            $name = $filterArg->name;
            $args[$name] = ($filterArg->filter)($args[$name]);
        }

        $args = array_values($args);

        $joinPoint->setArgs($args);
    }
}
