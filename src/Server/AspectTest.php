<?php
namespace Imi\Server;

use Imi\Aop\JoinPoint;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Annotation\After;
use Imi\Bean\Annotation\Around;
use Imi\Bean\Annotation\Aspect;
use Imi\Bean\Annotation\Before;
use Imi\Bean\Annotation\PointCut;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\AfterReturningJoinPoint;
use Imi\Bean\Annotation\AfterThrowing;
use Imi\Bean\Annotation\AfterReturning;

/**
 * @Aspect
 */
class AspectTest
{
	/**
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Server\*\Server::is*",
	 * 		}
	 * )
	 * @Before
	 * @After
	 * @param JoinPoint $a
	 * @return void
	 */
	public function testBeforeAndAfter(JoinPoint $joinPoint)
	{
		var_dump('前置和后置:' . $joinPoint->getType());
	}

	/**
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Server\*\Server::get*",
	 * 		}
	 * )
	 * @Around
	 * @return mixed
	 */
	public function testAround1(AroundJoinPoint $joinPoint)
	{
		echo '环绕1-before', PHP_EOL;
		$result = $joinPoint->proceed();
		echo '环绕1-after', PHP_EOL;
		return $result;
	}

	/**
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Server\*\Server::get*",
	 * 		}
	 * )
	 * @Around
	 * @return mixed
	 */
	public function testAround2(AroundJoinPoint $joinPoint)
	{
		echo '环绕2-before', PHP_EOL;
		$result = $joinPoint->proceed();
		echo '环绕2-after', PHP_EOL;
		return $result;
	}

	/**
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Server\*\Server::is*",
	 * 		}
	 * )
	 * @AfterReturning
	 * @param AfterReturningJoinPoint $joinPoint
	 * @return void
	 */
	public function testAfterReturning(AfterReturningJoinPoint $joinPoint)
	{
		var_dump('返回值拦截:' . $joinPoint->getType(), $joinPoint->getReturnValue());
		// $joinPoint->setReturnValue(true);
	}

	/**
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Server\*\Server::*",
	 * 		}
	 * )
	 * @AfterThrowing
	 * @param AfterThrowingJoinPoint $joinPoint
	 * @return void
	 */
	public function testAfterThrowing1(AfterThrowingJoinPoint $joinPoint)
	{
		$joinPoint->cancelThrow();
		var_dump('异常捕获:' . $joinPoint->getThrowable()->getMessage());
	}
}