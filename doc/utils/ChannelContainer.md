# 通道容器

**类名:** `Imi\Swoole\Util\Co\ChannelContainer`

一个通道（Channel）管理容器，通过一个 `id`，快速操作和管理通道。

> imi v1.2.0 版本新增

## 方法

### push

向通道中写入数据。

```php
ChannelContainer::push('id123', '数据');
ChannelContainer::push('id123', '数据', 3); // 超时 3 秒
```

### pop

从通道中读取数据。

```php
ChannelContainer::pop('id123');
ChannelContainer::pop('id123', 3); // 超时 3 秒
```

### finallyPop

从通道拿数据，并且释放通道

```php
ChannelContainer::finallyPop('id123');
ChannelContainer::finallyPop('id123', 3); // 超时 3 秒
```

### stats

获取通道的状态

```php
ChannelContainer::stats('id123');
```

### close

关闭通道。并唤醒所有等待读写的协程。

```php
ChannelContainer::close('id123');
```

### length

获取通道中的元素数量。

```php
ChannelContainer::length('id123');
```

### isEmpty

判断当前通道是否为空。

```php
ChannelContainer::isEmpty('id123');
```

### isFull

判断当前通道是否已满。

```php
ChannelContainer::isFull('id123');
```

### getChannel

获取 `\Swoole\Coroutine\Channel` 对象，不存在会自动创建并返回

```php
ChannelContainer::getChannel('id123');
```

### hasChannel

通道是否存在

```php
ChannelContainer::hasChannel('id123');
```

### removeChannel

移除通道

```php
ChannelContainer::removeChannel('id123');
```
