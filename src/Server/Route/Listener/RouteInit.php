<?php
namespace Imi\Server\Route\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Parser\ControllerParser;

/**
 * http服务器路由初始化
 * @Listener("IMI.MAIN_SERVER.WORK.START")
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
		$controllerParser = ControllerParser::getInstance();
		$server = $e->getTarget();
		if($server instanceof \Imi\Server\Http\Server)
		{
			$route = $server->getBean('HttpRoute');
		}
		else
		{
			return;
		}
		foreach($controllerParser->getByServer($server->getName()) as $className => $classItem)
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
					$route->addRuleAnnotation($routeItem, [function() use($server, $className){
						return $server->getBean($className);
					}, $methodName]);
				}
			}
		}
	}
}