<?php
namespace Imi\Db\Aop;

use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Db\Exception\DbException;
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
		$result = $this->parseDefer($joinPoint);
		if(false === $result)
		{
			$statement = $joinPoint->getTarget();
			throw new DbException('sql query error: [' . $statement->errorCode() . '] ' . $statement->errorInfo() . ' sql: ' . $statement->getSql());
		}
		return $result;
	}

	/**
	 * Statement 延迟收包
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\Db\Drivers\CoroutineMysql\Statement::__execute",
	 * 		}
	 * )
	 * @Around
	 * @return mixed
	 */
	public function statementDefer(AroundJoinPoint $joinPoint)
	{
		$statement = $joinPoint->getTarget();
		$client = $statement->getDb()->getInstance();
		$result = $this->parseDefer($joinPoint, $client);
		if(false === $result)
		{
			throw new DbException('sql query error: [' . $statement->errorCode() . '] ' . $statement->errorInfo() . ' sql: ' . $statement->getSql());
		}
		return $result;
	}

}