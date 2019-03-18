<?php
namespace Imi\Aop\Aop;

use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Util\ClassObject;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\Annotation\FilterArg;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @Aspect
 */
class FilterArgAop
{
    /**
     * 过滤方法参数
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             FilterArg::class
     *         }
     * )
     * @Before
     * @return mixed
     */
    public function parse(JoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());
        $filterArgs = AnnotationManager::getMethodAnnotations($class, $joinPoint->getMethod(), FilterArg::class);
        $args = ClassObject::convertArgsToKV($class, $joinPoint->getMethod(), $joinPoint->getArgs());

        foreach($filterArgs as $filterArg)
        {
            $args[$filterArg->name] = ($filterArg->filter)($args[$filterArg->name]);
        }

        $args = array_values($args);

        $joinPoint->setArgs($args);
    }
}
