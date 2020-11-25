<?php

namespace Imi\Test\Component\Aop\Aop;

use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\JoinPoint;
use Imi\Bean\BeanFactory;
use PHPUnit\Framework\Assert;

/**
 * @Aspect
 */
class BeforeAop
{
    /**
     * @Before
     * @PointCut(
     *     allow={
     *         "Imi\Test\Component\Aop\Classes\TestBeforeClass::test"
     *     }
     * )
     *
     * @return void
     */
    public function injectBefore(JoinPoint $joinPoint)
    {
        Assert::assertEquals([1], $joinPoint->getArgs());
        Assert::assertEquals('test', $joinPoint->getMethod());
        Assert::assertEquals(\Imi\Test\Component\Aop\Classes\TestBeforeClass::class, BeanFactory::getObjectClass($joinPoint->getTarget()));
        Assert::assertEquals('before', $joinPoint->getType());
        $joinPoint->setArgs([2]);
    }
}
