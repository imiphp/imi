IMI 的 Http Session 目前内嵌支持文件和 Redis 两种存储方式，当然你也可以自行扩展更多存储方式。

如果想要启用 Session，需要在配置文件中进行设置。

在服务器配置文件中：

```php
return [
	'beans'	=>	[
		'SessionManager'	=>	[
			// 指定Session存储驱动类
			'handlerClass'	=>	\Imi\Server\Session\Handler\File::class,
		],
		'SessionConfig'	=>	[
			// session 名称，默认为imisid
			// 'name'	=>	'',
			// 每次请求完成后触发垃圾回收的概率，默认为1%，可取值0~1.0，概率为0%~100%
			// 'gcProbability'	=>	0.1,
			// 最大存活时间，默认30天，单位秒
			// 'maxLifeTime'=>	0.1,
		],
		'SessionCookie'	=>	[
			// Cookie 的 生命周期，以秒为单位。
			'lifetime'	=>	86400 * 30,
			// 此 cookie 的有效 路径。 on the domain where 设置为“/”表示对于本域上所有的路径此 cookie 都可用。
			// 'path'		=>	'',
			// Cookie 的作用 域。 例如：“www.php.net”。 如果要让 cookie 在所有的子域中都可用，此参数必须以点（.）开头，例如：“.php.net”。
			// 'domain'	=>	'',
			// 设置为 TRUE 表示 cookie 仅在使用 安全 链接时可用。
			// 'secure'	=>	false,
			// 设置为 TRUE 表示 PHP 发送 cookie 的时候会使用 httponly 标记。
			// 'httponly'	=>	false,
		],
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