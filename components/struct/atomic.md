# Atomic

基于 `Swoole` 提供的 `Swoole\Atomic`，直接在配置文件中设置，就可以在`worker`进程中使用，数据互通。

使用时无需加锁！

## 配置方式

在配置文件中加入以下节
```php
'atomics'    =>    [
    // 定义名为name1的，初始值为0
    'name1',
    // 定义名为name2的，初始值为10
    'name2'    =>    10,
],
```

## 使用方式

```php
use \Imi\Util\AtomicManager;

// 获取 Swoole\Atomic 对象
$atomic = AtomicManager::getInstance('name1');

// 加1，返回结果
$number = AtomicManager::::add('name1');

// 加10，返回结果
$number = AtomicManager::::add('name1', 10);

// 减1，返回结果
$number = AtomicManager::::sub('name1');

// 减10，返回结果
$number = AtomicManager::::sub('name1', 10);

// 获取当前值
$number = AtomicManager::get('name1');

// 设置当前值为100
AtomicManager::get('name1', 100);

// -----------------------------------------------

// 如果当前数值等于100返回true，并将当前数值设置为1
// 如果当前数值不等于100返回false
$number = AtomicManager::cmpset('name1', 100, 1);

/**
 * 当原子计数的值为0时程序进入等待状态。另外一个进程调用wakeup可以再次唤醒程序。底层基于Linux Futex实现，使用此特性，可以仅用4字节内存实现一个等待、通知、锁的功能。
 * 超时返回false，错误码为EAGAIN，可使用swoole_errno函数获取
 * 成功返回true，表示有其他进程通过wakeup成功唤醒了当前的锁
 * 使用wait/wakeup特性时，原子计数的值只能为0或1，否则会导致无法正常使用
 * 当然原子计数的值为1时，表示不需要进入等待状态，资源当前就是可用。wait函数会立即返回true
 * @param string $name 原子计数对象名称
 * @param float $timeout 指定超时时间，默认为-1，表示永不超时，会持续等待直到有其他进程唤醒
 * @return bool
 */
$result = AtomicManager::wait('name1');
// 超时时间100毫秒
$result = AtomicManager::wait('name1', 0.1);

/**
 * 唤醒处于wait状态的其他进程。
 * 当前原子计数如果为0时，表示没有进程正在wait，wakeup会立即返回true
 * 当前原子计数如果为1时，表示当前有进程正在wait，wakeup会唤醒等待的进程，并返回true
 * 如果同时有多个进程处于wait状态，$n参数可以控制唤醒的进程数量
 * 被唤醒的进程返回后，会将原子计数设置为0，这时可以再次调用wakeup唤醒其他正在wait的进程
 * @param string $name 原子计数对象名称
 * @param integer $n
 * @return void
 */
AtomicManager::wakeup('name1'); // 第二个参数我也不知道干嘛的，swoole文档没写
```