# 不使用中间件

imi中内置实现了中间件+控制器方式开发tcp，在一些场景也可以选择不使用，直接监听服务器的receive事件，进行手动处理。

## 监听写法

```php
<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\App;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Swoole\Server\Event\Param\ReceiveEventParam;
use Imi\Swoole\Server\Event\Listener\IReceiveEventListener;
use Imi\Server\TcpServer\Message\ReceiveData;

/**
 * Receive事件前置处理
 * @ClassEventListener(className="Imi\Swoole\Server\TcpServer\Server",eventName="receive",priority=PHP_INT_MAX)
 */
class BeforeReceive implements IReceiveEventListener
{
	/**
	 * 事件处理方法
	 * @param ReceiveEventParam $e
	 * @return void
	 */
	public function handle(ReceiveEventParam $e)
	{
		// 如果服务器名不是主服务器就返回
		if('main' === $e->server->getName())
		{
			return;
		}
		var_dump($e->data);
	}
}
```