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

## 使用

### 写入缓存

```php
// 永久有效
Imi\Cache\CacheManager::set('缓存名称', 'key', 'value');

$ttl = 3600; // 缓存有效时间，单位：秒
Imi\Cache\CacheManager::set('缓存名称', 'key', 'value', $ttl);
```

### 批量写入缓存

```php
$values = [
	'k1'	=>	'v1',
	'k2'	=>	'v2',
];
Imi\Cache\CacheManager::setMultiple('缓存名称', $values);

$ttl = 3600; // 缓存有效时间，单位：秒
Imi\Cache\CacheManager::setMultiple('缓存名称', $values, $ttl);
```

### 读取缓存

```php
Imi\Cache\CacheManager::get('缓存名称', 'key');

Imi\Cache\CacheManager::get('缓存名称', 'key', 'default');
```

### 批量读取缓存

```php
$keys = ['k1', 'k2'];

Imi\Cache\CacheManager::getMultiple('缓存名称', $keys);

Imi\Cache\CacheManager::getMultiple('缓存名称', $keys, 'default');
```

### 删除缓存

```php
Imi\Cache\CacheManager::delete('缓存名称', 'key');
```

### 批量删除缓存

```php
$keys = ['k1', 'k2'];

Imi\Cache\CacheManager::deleteMultiple('缓存名称', $keys);
```

### 缓存是否存在

```php
Imi\Cache\CacheManager::has('缓存名称', 'key');
```

### 清空缓存

```php
Imi\Cache\CacheManager::clear('缓存名称');
```