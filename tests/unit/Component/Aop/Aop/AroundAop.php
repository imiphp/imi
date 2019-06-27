<?php
namespace Imi\Test\Component\Aop\Aop;

use Imi\Bean\BeanProxy;
use Imi\Bean\BeanFactory;
use Imi\Aop\AroundJoinPoint;
use PHPUnit\Framework\Assert;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;

/**
 * @Aspect
 */
class AroundAop
{
    /**
     * @Around
     * @PointCut(
     *     allow={
     *         "Imi\Test\Component\Aop\Classes\TestAroundClass::test"
     *     }
     * )
     *
     * @return void
     */
    public function injectAroundAop(AroundJoinPoint $joinPoint)
    {
        Assert::assertEquals([1], $joinPoint->getArgs());
        Assert::assertEquals('test', $joinPoint->getMethod());
        Assert::assertEquals(\Imi\Test\Component\Aop\Classes\TestAroundClass::class, BeanFactory::getObjectClass($joinPoint->getTarget()));
        Assert::assertEquals(BeanProxy::class, get_class($joinPoint->getThis()));
        Assert::assertEquals('around', $joinPoint->getType());
        $joinPoint->setArgs([2]);
        $result = $joinPoint->proceed();
        Assert::assertEquals(2, $result);
        $result = $joinPoint->proceed([3]);
        Assert::assertEquals(3, $result);
        return 4;
    }
}
