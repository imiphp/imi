<?php
namespace Imi\Util\Traits\Aspect;

use Imi\Aop\AroundJoinPoint;

/**
 * 处理 Swoole 协程客户端延迟收包
 * 适合用于 Aop 切入相应方法时
 */
trait TDefer
{
	public function parseDefer(AroundJoinPoint $joinPoint)
	{
		// 获取调用前的defer状态
		$isDefer = $joinPoint->getTarget()->getDefer();
		if(!$isDefer)
		{
			// 强制设为延迟收包
			$joinPoint->getTarget()->setDefer(true);
		}
		// 调用原方法
		$joinPoint->proceed();
		// 接收结果
		$result = $joinPoint->getTarget()->recv();
		if(!$isDefer)
		{
			// 设为调用前状态
			$joinPoint->getTarget()->setDefer(false);
		}
		return $result;
	}
}