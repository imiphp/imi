<?php
namespace Imi\Server\Http\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Parser;
use Imi\Bean\Annotation\ClassEventListener;

/**
 * a
 * a
 * adsfsdfds
 * @ClassEventListener(className="Imi\Server\Http\Server",eventName="request")
 */
class Route implements IEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(EventParam $e)
	{
		var_dump('Route', $e);
		$e->getData()['response']->write(time());
	}
}