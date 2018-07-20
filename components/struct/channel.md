# Channel

基于 `Swoole` 提供的 `Swoole\Channel`，直接在配置文件中设置，就可以在`worker`进程中使用，数据互通。

`Channel`可以作为队列使用。

## 配置方式

在配置文件中加入以下节
```php
'channels'    =>    [
    // 定义名为name1的，通道占用的内存的尺寸为64kb
    'name1'    =>    [64 * 1024],
],
```

## 使用方式

```php
use \Imi\Util\ChannelManager;

// 获取 Swoole\Channel对象
$channel = ChannelManager::getInstance('name1');

// 向队列中加入一个成员
ChannelManager::push('name1', 'test');
// 还支持数组，等一切可以被序列化的值
ChannelManager::push('name1', [1, 2, 3]);

// 弹出一个成员，如果没有值则为false
$result = ChannelManager::pop('name1');

// 获取通道的状态
$result = ChannelManager::stats('name');
/*
$result 格式如下：
[
    // 通道中的元素数量
    'queue_num'    =>    1,
    // 通道当前占用的内存字节数
    'queue_bytes'  =>    1024,
]
*/
```