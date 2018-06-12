IMI 中的缓存，符合PSR-16规范。使用时需要先配置驱动和实例。

## 配置

```php
<?php
return [
	'caches'	=>	[
		// 缓存名称
		'alias1'	=>	[
			// 缓存驱动类
			'handlerClass'	=>	\Imi\Cache\Handler\File::class,
			// 驱动实例配置
			'option'		=>	[
				'savePath'	=>	dirname(__DIR__, 3) . '/cache',
			],
		],
	],
];
```