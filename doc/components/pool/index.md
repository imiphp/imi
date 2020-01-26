# 连接池

由于 Swoole 的常驻内存特性，所以 imi 中实现了连接池。所有的数据库、Redis连接，都通过连接池去获取。

## 定义连接池

连接池的配置，可以写在项目配置文件中，也可以写在服务器配置文件中。

在配置文件中有一个`pools`节点，里面配置的是连接池。

同步池子仅在`task`进程使用，异步池子在`worker`进程使用。一般使用时无需自行判断在哪个进程，框架会自动帮你获取对应的连接。

## 获取连接

### 获取池子中的资源

`\Imi\Pool\PoolManager::getResource(string $name): IPoolResource`

`$name` 为池子名称

### 获取请求上下文资源

一个请求上下文通过此方法，只能获取同一个资源

`\Imi\Pool\PoolManager::getRequestContextResource(string $name): IPoolResource`

### 尝试获取资源

`\Imi\Pool\PoolManager::tryGetResource(string $name): IPoolResource|boolean`

### 回调方式使用资源

使用回调来使用池子中的资源，无需手动释放

```php
$result = \Imi\Pool\PoolManager::use($poolName, function($resource, \Swoole\Coroutine\Redis $redis) use($key){
	return $redis->get($key);
});
```

`$poolName`-池子名称
第二个参数为回调，接收两个参数，第一个资源本身，第二个为资源里面的实例。比如上面的是Redis
回调的返回值也会成为use方法的返回值

## 手动释放连接

`\Imi\Pool\PoolManager::releaseResource(IPoolResource $resource)`

## 自动释放连接

调用`\Imi\Pool\PoolManager::getRequestContextResource()`方法获取，当上下文被销毁时，会自动释放资源。