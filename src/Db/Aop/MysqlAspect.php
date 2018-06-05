<?php
namespace Imi\Db\Aop;

use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Util\Traits\Aspect\TDefer;

/**
 * @Aspect
 */
class MysqlAspect
{
	use TDefer;

	/**
	 * Db 延迟收包
	 * @PointCut(
	 * 		allow={
	 * 			"Swoole\Coroutine\MySQL::query",
	 * 			"Swoole\Coroutine\MySQL::prepare",
	 * 		}
	 * )
	 * @Around
	 * @return mixed
	 */
	public function defer(AroundJoinPoint $joinPoint)
	{
		return $this->parseDefer($joinPoint);
	}

	/**
	 * Statement 延迟收包
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Db\CoroutineMysql\Statement::execute",
	 * 		}
	 * )
	 * @Around
	 * @return mixed
	 */
	public function statementDefer(AroundJoinPoint $joinPoint)
	{
		$statement = $joinPoint->getTarget();
		$client = $statement->getDb()->getInstance();
		// 获取调用前的defer状态
		$isDefer = $client->getDefer();
		if(!$isDefer)
		{
			// 强制设为延迟收包
			$client->setDefer(true);
		}
		// 调用原方法
		$result = $joinPoint->proceed();
		if(!$isDefer)
		{
			// 设为调用前状态
			$client->setDefer(false);
		}
		return $result;
	}

}