# 后台任务

## 说明

在后台任务中，因为是运行在`task`进程，所以无法使用协程和协程客户端。如果你要操作数据库或`Redis`，一定要配置同步的连接池。

## 配置

项目配置文件中`mainServer.configs.task_worker_num`一定要配置为大于0，否则无法使用后台任务。

## 定义任务执行类

```php
<?php
namespace Test;

use Imi\Swoole\Task\TaskParam;
use Imi\Swoole\Task\Interfaces\ITaskHandler;

class Test implements ITaskHandler
{
    /**
     * 任务处理方法.
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
	{
		// 投递任务时的数据
		$data = $param->getData();
	}

    /**
     * 任务结束时触发.
     *
     * @param mixed $data
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
	{
		
	}
}
```

类名无所谓，实现`ITaskHandler`接口和两个方法即可。

## 投递任务

### 投递异步任务

```php
TaskManager::post(new TaskInfo(
	// 上面定义的类的实例
	new TestTask,
	// 执行任务的参数，构造方法可以接收一个数组
	new TaskParam([
		'time'	=>	time(),
	])
));
```

调用后立即返回，不影响下面代码的执行

### 同步投递任务

```php
TaskManager::postWait(new TaskInfo(
	// 上面定义的类的实例
	new TestTask,
	// 执行任务的参数，构造方法可以接收一个数组
	new TaskParam([
		'time'	=>	time(),
	])),
    // 最长等待时间，单位：秒
    0.5
);
```

投递任务后阻塞等待执行完毕或超时，返回值为任务执行结果或false

### 协程批量投递任务

```php
TaskManager::postCo([new TaskInfo(
	// 上面定义的类的实例
	new TestTask,
	// 执行任务的参数，构造方法可以接收一个数组
	new TaskParam([
		'time'	=>	time(),
	])
)],
// 最长等待时间，单位：秒
0.5);
```

传入数组和超时时间，投递后挂起协程，当全部执行完毕或超时后返回结果。返回值为数组，对应每个任务的执行结果。

## 注解

### 定义任务执行类

```php
<?php
namespace Test;

use Imi\Swoole\Task\TaskParam;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;

/**
 * @Task("testTask")
 */
class Test implements ITaskHandler
{
    /**
     * 任务处理方法.
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
	{
		// 投递任务时的数据
		$data = $param->getData();
	}

    /**
     * 任务结束时触发.
     *
     * @param mixed $data
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
	{
		
	}
}
```

类名无所谓，实现`ITaskHandler`接口和两个方法即可。

### 投递任务

#### 投递异步任务

```php
TaskManager::nPost(
// 任务名称
'testTask'
// 任务参数
, [
	'time'	=>	time(),
]
);
```

调用后立即返回，不影响下面代码的执行

#### 同步投递任务

```php
TaskManager::nPostWait(
	// 任务名称
	'testTask'
	// 任务参数
	, [
		'time'	=>	time(),
	]
	// 最长等待时间，单位：秒
	0.5
);
```

投递任务后阻塞等待执行完毕或超时，返回值为任务执行结果或false

#### 协程批量投递任务

```php
TaskManager::postCo([
	['testTask', ['time'=>time()]],
	['testTask', ['time'=>time()]],
],
// 最长等待时间，单位：秒
0.5);
```
