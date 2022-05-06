# 进程池-Swoole

[toc]

imi 中对进程池的创建和运行做了简单封装，基于`Swoole\Process\Pool`实现。

### 定义进程池

```php
<?php
namespace XinchengApi\api\ProcessPool;

use Imi\Swoole\Process\Annotation\ProcessPool;

/**
 * @ProcessPool(name="进程名称")
 */
class Sms extends \Imi\Swoole\Process\BasePoolProcess
{
	public function run(\Swoole\Process\Pool $pool, int $workerId, $name, $workerNum, $args, $ipcType, $msgQueueKey)
	{
	    // 做一些事情
	}
}
```

### 释放连接池

因为我们有些进程是用不到，或者不需要所有进程池的。进程池资源连着也是浪费，所有提供一个注解，用以释放。

在 `run()` 方法上使用 `@PoolClean` 注解即可，类：`Imi\Pool\Annotation\PoolClean`

`@PoolClean` 参数：

`mode` 模式，allow-白名单，deny-黑名单

`list=[]` 连接池名称列表。mode=allow则为保留的连接池，mode=deny则为关闭的连接池

### 注解

#### @ProcessPool

```php
/**
 * 进程池名称
 * @var string
 */
public $name;

/**
 * 进程数量
 *
 * @var int
 */
public $workerNum = 1;

/**
 * 进程间通信的模式，默认为0表示不使用任何进程间通信特性
 *
 * @var integer
 */
public $ipcType = 0;

/**
 * 消息队列key
 *
 * @var string
 */
public $msgQueueKey = null;
```

### 创建进程池

通过注解中的进程名称创建进程，返回`\Swoole\Process`类型的对象，需要手动调用`start()`方法来运行进程。

```php
/**
 * 创建进程池
 * 本方法无法在控制器中使用
 * 返回\Swoole\Process\Pool对象实例
 * 
 * @param string $name
 * @param int $workerNum 指定工作进程的数量
 * @param array $args
 * @param int $ipcType 进程间通信的模式，默认为0表示不使用任何进程间通信特性
 * @param string $msgQueueKey
 * @return \Swoole\Process\Pool
 */
public static function create($name, $workerNum = null, $args = [], $ipcType = 0, $msgQueueKey = null): \Swoole\Process\Pool
```

### 启动连接池进程

命令：`bin/imi-swoole process/pool 进程名`

其它参数可加上`-h`参数查看
