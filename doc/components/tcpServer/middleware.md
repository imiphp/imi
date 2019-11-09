# 中间件

开发者可以开发中间件类，对整个TCP请求和响应过程进行自定义处理。

> 注意！最好不要在中间件中使用类属性，可能会造成冲突！

### 定义中间件

```php
<?php
namespace Imi\Server\TcpServer\Middleware;

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
	public function process(IReceiveData $data, IReceiveHandler $handler)
	{
		
	}

}
```

### 全局中间件

```php
return [
	'beans'	=>	[
		// 中间件
		'TcpDispatcher'	=>	[
			'middlewares'	=>	[
				// 中间件
				\Imi\Server\Tcp\Middleware\RouteMiddleware::class,
			],
		],
	],
];
```

### 局部中间件

#### 注解使用

```php
<?php
namespace ImiDemo\TcpDemo\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Server\Route\Annotation\Tcp\TcpRoute;
use Imi\Server\Route\Annotation\Tcp\TcpAction;
use Imi\Server\Route\Annotation\Tcp\TcpController;

/**
 * 数据收发测试
 * @TcpController
 */
class Test extends \Imi\Controller\TcpController
{
	/**
	 * 登录
	 * 
	 * @TcpAction
	 * @TcpRoute({"action"="login"})
	 * @TcpMiddleware(XXX::class)
	 * @TcpMiddleware({XXX::class,XXX2::class})
	 * @return void
	 */
	public function login($data)
	{
	}
}
```

如上代码，同时支持设置单个和多个中间件

### 中间件分组

服务器 config.php：

```php
return [
	'middleware'    =>  [
        'groups'    =>  [
			// 组名
            'test'  =>  [
				// 中间件列表
                \Imi\Test\HttpServer\ApiServer\Middleware\Middleware4::class,
            ],
        ],
    ],
];
```
