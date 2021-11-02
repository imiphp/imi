# 路由

## 启用路由

服务器配置文件中加入中间件：

```php
return [
	'beans'	=>	[
		'UdpDispatcher'	=>	[
			'middlewares'	=>	[
				\Imi\Server\UdpServer\Middleware\RouteMiddleware::class,
			],
		],
	],
];
```

通过配置注入形式，实现非常灵活的配置，你甚至可以自己另外实现路由中间件，而不用被框架自带的中间件所影响，用哪些中间件都在你的掌控之中！

## 获取当前路由解析结果 (`routeResult`)

```php
$routeResult = RequestContext::get('routeResult');
```

`$routeResult` 定义：

```php
/**
 * 路由配置项
 *
 * @var \Imi\Server\UdpServer\Route\RouteItem
 */
public $routeItem;

/**
 * 参数
 *
 * @var array
 */
public $params;

/**
 * 回调
 *
 * @var callable
 */
public $callable;
```

`$routeResult->routeItem` 定义：

```php
/**
 * 注解
 *
 * @var \Imi\Server\UdpServer\Route\Annotation\UdpRoute
 */
public $annotation;

/**
 * 回调
 *
 * @var callable
 */
public $callable;

/**
 * 中间件列表
 *
 * @var array
 */
public $middlewares = [];

/**
 * 其它配置项
 *
 * @var array
 */
public $options;
```
