<?php
namespace Imi\Tool\Listener;

use Imi\App;
use Imi\Tool\Args;
use Imi\Util\Call;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Pool\PoolConfig;
use Imi\Event\EventParam;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Event\IEventListener;
use Imi\Tool\Parser\ToolParser;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.RUNTOOL")
 */
class Init implements IEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(EventParam $e)
	{
		if(!isset($_SERVER['argv'][1]))
		{
			exit;
		}
		$this->init();
		// cli参数初始化
		Args::init(2);
		// 工具名/操作名
		list($tool, $operation) = explode('/', $_SERVER['argv'][1]);
		// 获取回调
		$callable = ToolParser::getInstance()->getCallable($tool, $operation);
		// 执行参数
		$args = $this->getCallToolArgs($callable, $tool, $operation);
		// 执行工具操作
		Call::callUserFuncArray($callable, $args);
		\swoole_event_exit();
	}

	/**
	 * 初始化
	 * @return void
	 */
	private function init()
	{
		RequestContext::create();
		// 获取配置
		$pools = $caches = [];
		foreach(Helper::getMains() as $main)
		{
			$pools = array_merge($pools, $main->getConfig()['pools'] ?? []);
			$caches = array_merge($caches, $main->getConfig()['caches'] ?? []);
		}
		// 同步池子初始化
		foreach($pools as $name => $pool)
		{
			if(isset($pool['sync']))
			{
				$pool = $pool['sync'];
				PoolManager::addName($name, $pool['pool']['class'], new PoolConfig($pool['pool']['config']), $pool['resource']);
			}
		}
		// 缓存初始化
		foreach($caches as $name => $cache)
		{
			CacheManager::addName($name, $cache['handlerClass'], $cache['option']);
		}
	}

	private function getCallToolArgs($callable, $tool, $operation)
	{
		$methodRef = new \ReflectionMethod(get_parent_class($callable[0]), $callable[1]);
		$args = [];
		foreach($methodRef->getParameters() as $param)
		{
			if(Args::exists($param->name))
			{
				$args[] = Args::get($param->name);
			}
			else
			{
				if(!$param->isDefaultValueAvailable())
				{
					throw new \RuntimeException(sprintf('tool %s/%s param %s is required', $tool, $operation, $param->name));
				}
				$args[] = $param->getDefaultValue();
			}
		}
		return $args;
	}
}