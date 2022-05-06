# 路由

[toc]

## 启用路由

服务器配置文件中加入中间件：

```php
return [
	'beans'	=>	[
		// 配置 http 为了握手，仅 Swoole、Workerman 需要，如果不需要手动判断握手，可以不配置该项
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                'HandShakeMiddleware',
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
		],
		// WebSocket 配置
		'WebSocketDispatcher'	=>	[
			'middlewares'	=>	[
				\Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
			],
		],
	],
];
```

通过配置注入形式，实现非常灵活的配置，你甚至可以自己另外实现路由中间件，而不用被框架自带的中间件所影响，用哪些中间件都在你的掌控之中！

## 获取握手时的 uri

```php
$uri = ConnectionContext::get('uri'); // 字符串 uri
```

## 获取当前路由解析结果 (`routeResult`)

```php
$routeResult = RequestContext::get('routeResult');
```

`$routeResult` 定义：

```php
/**
 * 路由配置项
 *
 * @var \Imi\Server\WebSocket\Route\RouteItem
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
 * @var \Imi\Server\WebSocket\Route\Annotation\WSRoute
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
