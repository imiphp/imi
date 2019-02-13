# 中间件

开发者可以开发中间件类，对整个UDP请求和响应过程进行自定义处理。

> 注意！最好不要在中间件中使用类属性，可能会造成冲突！

### 定义中间件

```php
<?php
namespace Imi\Server\UdpServer\Middleware;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean
 */
class RouteMiddleware implements IMiddleware
{
	/**
	 * 处理方法
	 *
	 * @param IReceiveData $data
	 * @param IReceiveHandler $handle
	 * @return void
	 */
	public function process(IPacketData $data, IPacketHandler $handler)
	{
		
	}

}
```

### 全局中间件

```php
return [
	'beans'	=>	[
		// 中间件
		'UdpDispatcher'	=>	[
			'middlewares'	=>	[
				// 中间件
				\Imi\Server\Udp\Middleware\RouteMiddleware::class,
			],
		],
	],
];
```

### 局部中间件

#### 注解使用

```php
<?php
namespace ImiDemo\UdpDemo\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Server\Route\Annotation\Udp\UdpRoute;
use Imi\Server\Route\Annotation\Udp\UdpAction;
use Imi\Server\Route\Annotation\Udp\UdpController;

/**
 * 数据收发测试
 * @UdpController
 */
class Test extends \Imi\Controller\UdpController
{
	/**
	 * 登录
	 * 
	 * @UdpAction
	 * @UdpRoute({"action"="hello"})
	 * @UdpMiddleware(XXX::class)
	 * @UdpMiddleware({XXX::class,XXX2::class})
	 * @return void
	 */
	public function hello()
	{
		return [
			'time'	=>	date($this->data->getFormatData()->format),
		];
	}

}

```

如上代码，同时支持设置单个和多个中间件

