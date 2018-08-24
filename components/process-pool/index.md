# 进程池

IMI 中对进程池的创建和运行做了简单封装，基于`Swoole\Process\Pool`实现。

### 定义进程池

```php
<?php
namespace XinchengApi\api\ProcessPool;

use Imi\Process\Annotation\ProcessPool;

/**
 * @ProcessPool(name="进程名称")
 */
class Sms extends \Imi\Process\BasePoolProcess
{
	public function run(\Swoole\Process\Pool $pool, int $workerId, $name, $workerNum, $args, $ipcType, $msgQueueKey)
	{
	    // 做一些事情
	}
}
```

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

