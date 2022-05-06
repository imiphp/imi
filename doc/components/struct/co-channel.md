# 协程 Channel

[toc]

基于 `Swoole` 提供的 `Swoole\Coroutine\Channel`，直接在配置文件中设置，就可以在`worker`进程中使用，数据互通。

`Swoole\Coroutine\Channel`可以作为队列使用，支持多生产者协程和多消费者协程。底层自动实现了协程的切换和调度。

> 仅 Swoole 可用

## 配置方式

在项目配置文件中加入以下节
```php
'coroutineChannels'    =>    [
    // 定义名为name1的，通道占用的内存的尺寸为64kb
    // 当设置为0时，底层将不再设置缓冲区，push和pop操作会立即挂起当前协程。
    'name1'    =>    [64 * 1024],
],
```

## 使用方式

```php
use \Imi\Swoole\Util\CoroutineChannelManager;

// 获取 Swoole\Coroutine\Channel 对象
$channel = CoroutineChannelManager::getInstance('name1');

// 向队列中加入一个成员
CoroutineChannelManager::push('name1', 'test');
// 还支持数组，等一切可以被序列化的值
CoroutineChannelManager::push('name1', [1, 2, 3]);

// 弹出一个成员，如果没有值则为false
$result = CoroutineChannelManager::pop('name1');

// 获取通道的状态
$result = CoroutineChannelManager::stats('name');
/*
$result 格式如下：
[
    // 消费者数量，表示当前通道为空，有N个协程正在等待其他协程调用push方法生产数据
    'consumer_num'    =>    1,
    // 生产者数量，表示当前通道已满，有N个协程正在等待其他协程调用pop方法消费数据
    'producer_num'  =>    1,
    // 通道中的元素数量
    'queue_num'    =>    10,
    // 通道当前占用的内存字节数
    'queue_bytes'    =>    1024,
]
*/

// 关闭通道。并唤醒所有等待读写的协程。
CoroutineChannelManager::close('name1');

```