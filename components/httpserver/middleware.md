# 中间件

IMI 框架遵循 PSR-7、PSR-15 标准，使用中间件来实现路由。

开发者也可以开发中间件类，对整个请求和响应过程进行自定义处理。

### 定义中间件

```php
use Imi\Bean\Annotation\Bean;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @Bean
 */
class TestMiddleware implements MiddlewareInterface
{
	/**
	 * 处理方法
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 * @return ResponseInterface
	 */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// 前置处理
		
		// 先执行其它中间件
		$response = $handler->handle($request);
		
		// 后置处理
		
		return $response;
	}
}
```

### 配置启用中间件

```php
return [
	'beans'	=>	[
		// 中间件
		'HttpDispatcher'	=>	[
			'middlewares'	=>	[
				// 中间件
				\Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
			],
		],
	],
];
```
