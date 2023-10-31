<?php

declare(strict_types=1);

namespace Imi\Aop\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\InjectArg;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;

#[Aspect(priority: \Imi\Util\ImiPriority::IMI_MAX)]
class InjectArgAop
{
    /**
     * 方法参数注入.
     *
     * @return mixed
     */
    #[PointCut(type: \Imi\Aop\PointCutType::ANNOTATION, allow: [\Imi\Aop\Annotation\InjectArg::class])]
    #[Around]
    public function parse(AroundJoinPoint $joinPoint)
    {
        $class = BeanFactory::getObjectClass($joinPoint->getTarget());
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
