<?php
namespace Imi\Server\Route\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Parser\ControllerParser;
use Imi\ServerManage;

/**
 * http服务器路由初始化
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class RouteInit implements IEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(EventParam $e)
	{
		$this->parseAnnotations($e);
		$this->parseConfigs($e);
	}

	/**
	 * 处理注解路由
	 * @return void
	 */
	private function parseAnnotations(EventParam $e)
	{
		$controllerParser = ControllerParser::getInstance();
		foreach(ServerManage::getServers() as $name => $server)
		{
			$route = $server->getBean('HttpRoute');
			foreach($controllerParser->getByServer($name) as $className => $classItem)
			{
				$classAnnotation = $classItem['annotation'];
				foreach($classItem['methods'] as $methodName => $methodItem)
				{
					if(!isset($methodItem['routes']))
					{
						$methodItem['routes'] = [
							new Route([
								'url'	=>	$methodName,
							])
						];
					}
					foreach($methodItem['routes'] as $routeItem)
					{
						if(null === $routeItem->url)
						{
							$routeItem->url = $methodName;
						}
						if((!isset($routeItem->url[0]) || '/' !== $routeItem->url[0]) && '' != $classAnnotation->prefix)
						{
							$routeItem->url = $classAnnotation->prefix . $routeItem->url;
						}
						$route->addRuleAnnotation($routeItem, new RouteCallable($className, $methodName));
					}
				}
			}
		}
	}

	/**
	 * 处理配置文件路由
	 * @return void
	 */
	private function parseConfigs(EventParam $e)
	{
		$server = $e->getTarget();
		if($server instanceof \Imi\Server\Http\Server)
		{
			$route = $server->getBean('HttpRoute');
		}
		else
		{
			return;
		}
		foreach(Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $url => $routeOption)
		{
			$routeAnnotation = new Route($routeOption['route'] ?? []);
			if(isset($routeOption['callback']))
			{
				$callable = $routeOption['callback'];
			}
			else
			{
				$callable = new RouteCallable($routeOption['controller'], $routeOption['method']);
			}
			$route->addRuleAnnotation($routeAnnotation, $callable);
		}
	}
}