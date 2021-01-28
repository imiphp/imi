# 不使用中间件

imi中内置实现了中间件+控制器方式开发tcp，在一些场景也可以选择不使用，直接监听服务器的packet事件，进行手动处理。

## 监听写法

```php
<?php
namespace Imi\Swoole\Server\UdpServer\Listener;

use Imi\Server\ServerManager;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Swoole\Server\Event\Param\PacketEventParam;
use Imi\Server\UdpServer\Message\PacketData;
use Imi\Swoole\Server\Event\Listener\IPacketEventListener;

/**
 * Packet事件前置处理
 * @ClassEventListener(className="Imi\Server\UdpServer\Server",eventName="packet",priority=PHP_INT_MAX)
 */
class BeforePacket implements IPacketEventListener
{
	/**
	 * 事件处理方法
	 * @param PacketEventParam $e
	 * @return void
	 */
	public function handle(PacketEventParam $e)
	{
		// 如果服务器名不是主服务器就返回
		if('main' !== $e->getTarget()->getName())
		{
			return;
		}
		var_dump($e->data);

	}
}
```