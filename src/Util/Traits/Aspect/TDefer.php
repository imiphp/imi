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
		$client = $joinPoint->getTarget();
		// 获取调用前的defer状态
		$isDefer = $client->getDefer();
		if(!$isDefer)
		{
			// 强制设为延迟收包
			$client->setDefer(true);
		}
		// 调用原方法
		$joinPoint->proceed();
		// 接收结果
		$result = $client->recv();
		if(!$isDefer)
		{
			// 设为调用前状态
			$client->setDefer(false);
		}
		return $result;
	}
}