<?php
namespace Imi\Tool\Listener;

use Imi\App;
use Imi\Util\Args;
use Imi\Main\Helper;
use Imi\Tool\ArgType;
use Imi\RequestContext;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Event\EventParam;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Tool\Annotation\Arg;
use Imi\Event\IEventListener;
use Imi\Tool\Parser\ToolParser;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.INITED")
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
		try{
			if(!isset($_SERVER['argv'][1]))
			{
				throw new \RuntimeException(sprintf('tool args error!'));
			}
			$this->init();
			// cli参数初始化
			Args::init(2);
			// 工具名/操作名
			list($tool, $operation) = explode('/', $_SERVER['argv'][1]);
			// 获取回调
			$callable = ToolParser::getInstance()->getCallable($tool, $operation);
			if(null === $callable)
			{
				Annotation::getInstance()->init([Helper::getMain(App::getNamespace())]);
			}
			$callable = ToolParser::getInstance()->getCallable($tool, $operation);
			if(null === $callable)
			{
				throw new \RuntimeException(sprintf('tool %s does not exists!', $_SERVER['argv'][1]));
			}
			if(Args::get('h'))
			{
				// 帮助
				$className = get_parent_class($callable[0]);
				$refClass = new \ReflectionClass($className);
				$required = [];
				$other = [];
				foreach(ToolParser::getInstance()->getData()['class'][$className]['Methods'][$callable[1]]['Args'] ?? [] as $arg)
				{
					if($arg->required)
					{
						$required[] = $arg;
					}
					else
					{
						$other[] = $arg;
					}
				}
				echo '工具名称：', $tool, '/', $operation, PHP_EOL, PHP_EOL;
				echo $this->parseComment($refClass->getMethod($callable[1])->getDocComment()), PHP_EOL;
				if(isset($required[0]))
				{
					echo PHP_EOL, '必选参数：', PHP_EOL;
					foreach($required as $arg)
					{
						echo '-', $arg->name, ' ', $arg->comments, PHP_EOL;
					}
				}
				if(isset($other[0]))
				{
					echo PHP_EOL, '可选参数：', PHP_EOL;
					foreach($other as $arg)
					{
						echo '-', $arg->name, ' ', $arg->comments, PHP_EOL;
					}
				}
			}
			else
			{
				// 执行参数
				$args = $this->getCallToolArgs($callable, $tool, $operation);
				// 执行工具操作
				call_user_func_array($callable, $args);
				swoole_event_wait();
			}
		}
		catch(\Throwable $ex)
		{
			echo $ex->getMessage(), PHP_EOL;
		}
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

	/**
	 * 获取执行参数
	 * @param callable $callable
	 * @param string $tool
	 * @param string $operation
	 * @return array
	 */
	private function getCallToolArgs($callable, $tool, $operation)
	{
		$className = get_parent_class($callable[0]);
		$methodRef = new \ReflectionMethod($className, $callable[1]);
		$args = [];
		foreach(ToolParser::getInstance()->getData()['class'][$className]['Methods'][$methodRef->name]['Args'] ?? [] as $annotation)
		{
			if(Args::exists($annotation->name))
			{
				$value = $this->parseArgValue(Args::get($annotation->name), $annotation);
			}
			else if($annotation->required)
			{
				throw new \InvalidArgumentException(sprintf('tool %s/%s param %s is required', $tool, $operation, $annotation->name));
			}
			else
			{
				$value = $annotation->default;
			}
			$args[] = $value;
		}
		return $args;
	}

	/**
	 * 处理参数值
	 * @param string $value
	 * @param Arg $annotation
	 * @return mixed
	 */
	private function parseArgValue($value, Arg $annotation)
	{
		switch($annotation->type)
		{
			case ArgType::STRING:
				break;
			case ArgType::INT:
				$value = (int)$value;
				break;
			case ArgType::FLOAT:
			case ArgType::DOUBLE:
				$value = (float)$value;
				break;
			case ArgType::BOOL:
			case ArgType::BOOLEAN:
				$value = (bool)json_decode($value);
				break;
			case ArgType::ARRAY:
				$value = explode(',', $value);
				break;
		}
		return $value;
	}

	/**
	 * 处理注释
	 * @param string $content
	 * @return string
	 */
	private function parseComment($content)
	{
		return trim(preg_replace('/@.+\n/', '', preg_replace('/\/*\s*\*\s*\/*/', PHP_EOL, $content)));
	}
}