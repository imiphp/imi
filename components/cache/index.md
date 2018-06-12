IMI 中的缓存，符合PSR-16规范。使用时需要先配置驱动和实例。

## 配置

### 文件缓存

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
				'savePath'	=>	'文件缓存保存路径',
				// 保存文件名处理回调，一般可以不写
				// 'saveFileNameCallback'	=>	function($savePath, $key){
				// 	return '';
				// },
			],
		],
	],
];
```

### Redis 缓存

```php
return [
	'caches'	=>	[
		// 缓存名称
		'alias1'	=>	[
			// 缓存驱动类
			'handlerClass'	=>	\Imi\Cache\Handler\Redis::class,
			// 驱动实例配置
			'option'		=>	[
				'poolName'	=>	'连接池名称',
			],
		],
	],
];
```

> 文件缓存不支持分布式存储，请慎重选择！