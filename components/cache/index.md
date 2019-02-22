# 缓存

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

> 文件缓存不支持分布式存储，请慎重选择！

### Redis 缓存

> 使用 redis 的 set 和 get 方法，支持 ttl

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

### Redis Hash 缓存

> 使用 redis 的 hash，即 hset 和 hget 方法，不支持ttl

```php
return [
	'caches'	=>	[
		// 缓存名称
		'alias1'	=>	[
			// 缓存驱动类
			'handlerClass'	=>	\Imi\Cache\Handler\RedisHash::class,
			// 驱动实例配置
			'option'		=>	[
				'poolName'	=>	'连接池名称',
				'separator' =>	'分隔符，分隔 hash key和 member，默认为->',
			],
		],
	],
];
```

`RedisHash` 缓存使用时需要注意，key格式为`hashkey->member`，即为`$redis->hget('hashkey', 'member')`

## 手动使用

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

## 注解使用

除了手动控制缓存读写，你还可以使用注解来做，真的炒鸡方便哦！

首先来认识一下 `imi` 中的缓存注解吧！

### @Cacheable

调用方法前检测是否存在缓存，如果存在直接返回；不存在则执行方法体，然后将返回值存入缓存

**用法：**

基本用法：

`@Cacheable(name="缓存器名，为null则取cache.default配置", key="缓存键名，支持{id}、{data.name}、{:args}(所有参数的hash值)形式，代入参数，如果为null，则使用类名+方法名+全部参数，序列化后hash", ttl="超时时间，单位秒", lockable="Lock 注解，在调用方法体前后加锁", hashMethod="md5")`

防止缓存击穿：

```php
/*
 * @Cacheable(
 *   key="index:{page}",
 *   ttl=10,
 *   lockable=@Lockable(
 *     id="index:{page}",
 *     waitTimeout=999999,
 *   ),
 *   preventBreakdown=true,
 * )
 */
```

`lockable` 用法请参考 [Lock](/components/lock/index.html)

`preventBreakdown` 如果设为 `true`，会在获得锁后，尝试获取缓存，如果缓存存在则不再执行方法体

如果 `preventBreakdown` 设为 `true`，并且`lockable`中也设定了`afterLock`，优先级为：`afterLock > 缓存检测`

### @CacheEvict

缓存驱逐注解，方法体执行时，将指定缓存清除

**用法：**

在方法执行前删除缓存：

`@CacheEvict(name="同上", key="同上", beforeInvocation=true, hashMethod="md5")`

在方法执行后删除缓存：

`@CacheEvict(name="同上", key="同上", hashMethod="md5")`

### @CachePut

方法体执行后，将返回值存入缓存

**用法：**

将方法返回值全部写入缓存：

`@CachePut(name="同上", key="同上", ttl="同上", hashMethod="md5")`

将方法返回值的一部分写入缓存：

`@CachePut(name="同上", key="同上", ttl="同上", value="a.b", hashMethod="md5")`

上面的注解，如果方法返回值为：

```php
[
    'a' =>  [
        'b' =>  123,
    ],
]
```

则会将`123`写入缓存。