# 中间件

[toc]

开发者可以开发中间件类，对整个WebSocket请求和响应过程进行自定义处理。

> 注意！最好不要在中间件中使用类属性，可能会造成冲突！

## 定义中间件

```php
<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\MessageHandler;
use Imi\Server\WebSocket\IMessageHandler;

/**
 * @Bean
 */
class RouteMiddleware implements IMiddleware
{
	/**
	 * 处理方法
	 *
	 * @param IFrame $frame
	 * @param IMessageHandler $handler
	 * @return void
	 */
    public function process(IFrame $frame, IMessageHandler $handler)
	{
        // 前置处理
        
        // 先执行其它中间件
        $result = $handler->handle($frame);
        
        // 后置处理
        
        return $result;
	}

}
```

### 全局中间件

```php
return [
	'beans'	=>	[
		// 中间件
		'WebSocketDispatcher'	=>	[
			'middlewares'	=>	[
				// 中间件
				\Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
			],
		],
	],
];
```

### 局部中间件

#### 注解使用

```php
<?php
namespace ImiDemo\WebSocketDemo\MainServer\WSController\Index;

use Imi\ConnectionContext;
use Imi\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSMiddleware;

/**
 * 数据收发测试
 * @WSController
 */
class Test extends WebSocketController
{
	/**
	 * test
	 * 
	 * @WSAction
	 * @WSRoute({"action"="login"})
	 * @WSMiddleware(XXX::class)
	 * @WSMiddleware({XXX::class,XXX2::class})
	 * @return void
	 */
	public function test($data)
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
