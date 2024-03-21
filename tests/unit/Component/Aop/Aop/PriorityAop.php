<?php

declare(strict_types=1);

namespace Imi\Test\Component\Aop\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;

#[Aspect(1000)]
class PriorityAop
{
    #[Around]
    #[PointCut(allow: ['Imi\\Test\\Component\\Aop\\Classes\\TestPriorityClass::test'])]
    public function aop1(AroundJoinPoint $joinPoint): mixed
    {
        $args = $joinPoint->getArgs();
        $args[0][] = 1;
        $joinPoint->proceed();

        return 1;
    }

    #[Around]
    #[PointCut(allow: ['Imi\\Test\\Component\\Aop\\Classes\\TestPriorityClass::test'], priority: 500)]
    public function aop2(AroundJoinPoint $joinPoint): mixed
    {
        $args = $joinPoint->getArgs();
        $args[0][] = 2;
        $joinPoint->proceed();

        return 2;
    }

    #[Around]
    #[PointCut(allow: ['Imi\\Test\\Component\\Aop\\Classes\\TestPriorityClass::test'], priority: 1500)]
    public function aop3(AroundJoinPoint $joinPoint): mixed
    {
        $args = $joinPoint->getArgs();
        $args[0][] = 3;
        $joinPoint->proceed();

        return 3;
    }
}
