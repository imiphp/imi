<?php

declare(strict_types=1);

namespace Imi\Aop\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\InjectArg;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Util\ClassObject;

/**
 * @Aspect(Imi\Util\ImiPriority::IMI_MAX)
 */
class InjectArgAop
{
    /**
     * 方法参数注入.
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             InjectArg::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parse(AroundJoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        /** @var InjectArg[] $injectArgs */
        $injectArgs = AnnotationManager::getMethodAnnotations($class, $method, InjectArg::class);
        $args = ClassObject::convertArgsToKV($class, $method, $joinPoint->getArgs());

        foreach ($injectArgs as $injectArg)
        {
            $args[$injectArg->name] = $injectArg->value;
        }

        $args = array_values($args);

        return $joinPoint->proceed($args);
    }
}
