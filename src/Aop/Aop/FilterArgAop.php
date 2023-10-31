<?php

declare(strict_types=1);

namespace Imi\Aop\Aop;

use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\FilterArg;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\JoinPoint;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;

#[Aspect]
class FilterArgAop
{
    /**
     * 过滤方法参数.
     *
     * @return mixed
     */
    #[PointCut(type: \Imi\Aop\PointCutType::ANNOTATION, allow: ['Imi\\Aop\\Annotation\\FilterArg'])]
    #[Before]
    public function parse(JoinPoint $joinPoint)
    {
        $class = BeanFactory::getObjectClass($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        /** @var FilterArg[] $filterArgs */
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
