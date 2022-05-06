# 不使用中间件

[toc]

imi中内置实现了中间件+控制器方式开发websocket，在一些场景也可以选择不使用，直接监听服务器的message事件，进行手动处理。

## 监听写法

```php
<?php
namespace ImiDemo\WebSocketDemo\MainServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Swoole\Server\Event\Param\MessageEventParam;
use Imi\Swoole\Server\Event\Listener\IMessageEventListener;

/**
 * 监听message事件，可以自己做处理
 * 如果不需要默认的处理方式，在配置文件中，把WebSocketDispatcher-middlewares下的中间件去除
 * 
 * @ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server",eventName="message")
 */
class OnMessage implements IMessageEventListener
{
	/**
	 * 事件处理方法
	 * @param MessageEventParam $e
	 * @return void
	 */
	public function handle(MessageEventParam $e): void
	{
		// 如果服务器名不是主服务器就返回
		if('main' !== $e->getTarget()->getName())
		{
			return;
		}
		var_dump($e->frame->data);
		// $e->server->getSwooleServer()->push($e->frame->fd, '返回信息');
	}
}
```