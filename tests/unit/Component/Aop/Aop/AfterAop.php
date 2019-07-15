<?php
namespace Imi\Test\Component\Aop\Aop;

use Imi\Aop\JoinPoint;
use Imi\Bean\BeanProxy;
use Imi\Bean\BeanFactory;
use Imi\Aop\Annotation\After;
use PHPUnit\Framework\Assert;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;

/**
 * @Aspect
 */
class AfterAop
{
    /**
     * @After
     * @PointCut(
     *     allow={
     *         "Imi\Test\Component\Aop\Classes\TestAfterClass::test"
     *     }
     * )
     *
     * @return void
     */
    public function injectAfterAop(JoinPoint $joinPoint)
    {
        Assert::assertEquals([1], $joinPoint->getArgs());
        Assert::assertEquals('test', $joinPoint->getMethod());
        Assert::assertEquals(\Imi\Test\Component\Aop\Classes\TestAfterClass::class, BeanFactory::getObjectClass($joinPoint->getTarget()));
        Assert::assertEquals(BeanProxy::class, get_class($joinPoint->getThis()));
        Assert::assertEquals('after', $joinPoint->getType());
        $joinPoint->setArgs([2]);
    }
}
