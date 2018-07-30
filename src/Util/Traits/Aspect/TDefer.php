<?php
namespace Imi\Util\Traits\Aspect;

use Imi\Aop\AroundJoinPoint;

/**
 * 处理 Swoole 协程客户端延迟收包
 * 适合用于 Aop 切入相应方法时
 */
trait TDefer
{
	private static $hasMulti = [];

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
		if($joinPoint->proceed())
		{
			$lowerMethod = strtolower($joinPoint->getMethod());

			$isRecv = true;

			if($this->hasMulti($joinPoint->getTarget()))
			{
				if('exec' === $lowerMethod)
				{
					$this->setHasMulti($joinPoint->getTarget(), false);
				}
				else
				{
					$isRecv = false;
				}
			}
			else
			{
				if('multi' === $lowerMethod)
				{
					$this->setHasMulti($joinPoint->getTarget(), true);
					$isRecv = false;
				}
			}

			if($isRecv)
			{
				// 接收结果
				$result = $client->recv();
			}
			else
			{
				$result = true;
			}

			if(!$isDefer)
			{
				// 设为调用前状态
				$client->setDefer(false);
			}
			return $result;
		}
		else
		{
			return false;
		}
	}

	private function hasMulti($redis)
	{
		$hash = spl_object_hash($redis);
		return static::$hasMulti[$hash] ?? false;
	}

	private function setHasMulti($redis, $has)
	{
		$hash = spl_object_hash($redis);
		static::$hasMulti[$hash] = $has;
	}
}