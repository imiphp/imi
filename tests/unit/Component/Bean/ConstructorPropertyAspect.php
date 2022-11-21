<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\JoinPoint;
use Imi\Log\Log;

/**
 * @Aspect
 */
class ConstructorPropertyAspect
{
    /**
     * @After
     *
     * @PointCut(
     *     allow={
     *         "Imi\Test\Component\Bean\ConstructorPropertyBean::__construct"
     *     }
     * )
     */
    public function injectAfterAop(JoinPoint $joinPoint): void
    {
        Log::info('after ConstructorPropertyBean::__construct()');
    }
}
