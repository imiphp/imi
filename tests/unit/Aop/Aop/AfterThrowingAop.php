<?php
namespace Imi\Test\Aop\Aop;

use Imi\Bean\BeanProxy;
use Imi\Bean\BeanFactory;
use PHPUnit\Framework\Assert;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\Annotation\AfterThrowing;

/**
 * @Aspect
 */
class AfterThrowingAop
{
    /**
     * @AfterThrowing
     * @PointCut(
     *     allow={
     *         "Imi\Test\Aop\Classes\TestAfterThrowingClass::testCancelThrow"
     *     }
     * )
     *
     * @return void
     */
    public function injectAfterThrowingAopCancelThrow(AfterThrowingJoinPoint $joinPoint)
    {
        Assert::assertEquals([], $joinPoint->getArgs());
        Assert::assertEquals('testCancelThrow', $joinPoint->getMethod());
        Assert::assertEquals(\Imi\Test\Aop\Classes\TestAfterThrowingClass::class, BeanFactory::getObjectClass($joinPoint->getTarget()));
        Assert::assertEquals(BeanProxy::class, get_class($joinPoint->getThis()));
        Assert::assertEquals('afterThrowing', $joinPoint->getType());

        $throwable = $joinPoint->getThrowable();
        Assert::assertEquals('test', $throwable->getMessage());

        $joinPoint->cancelThrow();
    }

    /**
     * @AfterThrowing
     * @PointCut(
     *     allow={
     *         "Imi\Test\Aop\Classes\TestAfterThrowingClass::testNotCancelThrow"
     *     }
     * )
     *
     * @return void
     */
    public function injectAfterThrowingAopNotCancelThrow(AfterThrowingJoinPoint $joinPoint)
    {
        Assert::assertEquals([], $joinPoint->getArgs());
        Assert::assertEquals('testNotCancelThrow', $joinPoint->getMethod());
        Assert::assertEquals(\Imi\Test\Aop\Classes\TestAfterThrowingClass::class, BeanFactory::getObjectClass($joinPoint->getTarget()));
        Assert::assertEquals(BeanProxy::class, get_class($joinPoint->getThis()));
        Assert::assertEquals('afterThrowing', $joinPoint->getType());

        $throwable = $joinPoint->getThrowable();
        Assert::assertEquals('test', $throwable->getMessage());
    }
}
