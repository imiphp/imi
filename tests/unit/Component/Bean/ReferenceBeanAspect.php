<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\AfterReturning;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\JoinPoint;
use Imi\Log\Log;

#[Aspect]
class ReferenceBeanAspect
{
    #[Before]
    #[PointCut(allow: ['Imi\\Test\\Component\\Bean\\ReferenceBean::*'])]
    public function before(JoinPoint $joinPoint): void
    {
        Log::info(sprintf('before ReferenceBean::%s()', $joinPoint->getMethod()));
    }

    #[After]
    #[PointCut(allow: ['Imi\\Test\\Component\\Bean\\ReferenceBean::*'])]
    public function after(JoinPoint $joinPoint): void
    {
        Log::info(sprintf('after ReferenceBean::%s()', $joinPoint->getMethod()));
    }

    /**
     * @return mixed
     */
    #[Around]
    #[PointCut(allow: ['Imi\\Test\\Component\\Bean\\ReferenceBean::*'])]
    public function &around(AroundJoinPoint $joinPoint)
    {
        Log::info(sprintf('around ReferenceBean::%s()', $joinPoint->getMethod()));

        if ('testParams' === $joinPoint->getMethod())
        {
            $args = $joinPoint->getArgs();
            ++$args[0];
            $joinPoint->setArgs($args);
        }

        return $joinPoint->proceed(null, true);
    }

    #[AfterReturning]
    #[PointCut(allow: ['Imi\\Test\\Component\\Bean\\ReferenceBean::*'])]
    public function afterReturning(AfterReturningJoinPoint $joinPoint): void
    {
        Log::info(sprintf('afterReturning ReferenceBean::%s()', $joinPoint->getMethod()));
    }

    #[AfterThrowing]
    #[PointCut(allow: ['Imi\\Test\\Component\\Bean\\ReferenceBean::*'])]
    public function afterThrowing(AfterThrowingJoinPoint $joinPoint): void
    {
        Log::info(sprintf('afterThrowing ReferenceBean::%s()', $joinPoint->getMethod()));
    }
}
