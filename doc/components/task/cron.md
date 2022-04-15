# 定时任务

## 说明

在实际项目中，我们经常会有一些任务是需要定时执行的。

虽然有 `cron`、`crontab`、`systemd` 等系统级内置的，定时任务工具存在。

但是他们的一些让人掉头发的配置写法，以及增加运维心智负担，无法适应多实例部署场景等等原因，势必需要在 imi 框架中提供这个功能。

## 设计

imi 通过增加一个 `CronProcess` 进程用于定时任务的调度和执行，使用 `Redis` 作为数据存储。

定时任务支持在以下进程中执行： `Task` 进程、`Worker` 进程，也支持新运行一个 `Process` 进程。

支持设置某进程在当前实例/多实例中只运行一个。

## 使用

### 启用定时任务进程

必须在项目 `config.php` 的 `beans` 中加入配置启用定时任务进程，否则所有定时任务都无法生效。

配置代码：

```php
'AutoRunProcessManager'   =>  [
    'processes' =>  [
        'CronProcess',
    ],
],
```

### 定义任务

#### Task 任务
使用task定时任务时，需要在项目config.php中的服务器配置里，开启`task_worker_num`参数，否则会报下面的错：

Uncaught ErrorException: Swoole\Server::task(): task method can't be executed without task worker
```php
// 主服务器配置
'mainServer'    =>    [
    'namespace'    =>    'ImiApp\ApiServer',
    'type'        =>    Imi\Server\Type::HTTP,
    'host'        =>    '0.0.0.0',
    'port'        =>    9501,
    'configs'    =>    [
        // 'worker_num'        =>  8,
        'task_worker_num'   =>  16, // 必须开启这个参数，否则报错
    ],
],
```

与异步任务写法基本一致，多了`@Cron`注解，并且需要**上报任务完成**！
```php
<?php
namespace Imi\Test\HttpServer\Cron;

use Imi\Swoole\Task\TaskParam;
use Imi\Cron\Annotation\Cron;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Task\Interfaces\ITaskHandler;

/**
 * @Cron(id="TaskCron", second="3n", data={"id":"TaskCron"})
 * @Task("CronTask1")
 */
class TaskCron implements ITaskHandler
{
    /**
     * 任务处理方法.
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
    {
        // 上报任务完成
        CronUtil::reportCronResult($param->getData()['id'], true, '');
        return date('Y-m-d H:i:s');
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

#### Process 任务

与进程写法基本一致，多了`@Cron`注解，并且需要**上报任务完成**！

```php
<?php
namespace Imi\Test\HttpServer\Cron;

use Imi\Util\Args;
use Imi\Swoole\Process\Contract\IProcess;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Process\Annotation\Process;
use Swoole\Event;

/**
 * @Cron(id="CronProcess1", second="3n")
 * @Process("CronProcess1")
 */
class TaskProcess implements IProcess
{
    public function run(\Swoole\Process $process): void
    {
        $success = false;
        $message = '';
        try {
            // 做一些事情
            $success = true;
        } catch(\Throwable $th) {
            $message = $th->getMessage();
            throw $th;
        } finally {
            // 上报任务完成
            CronUtil::reportCronResult($id, $success, $message);
        }
    }

}
```

#### 协程任务

实现 `Imi\Cron\Contract\ICronTask` 接口、`run()` 方法，无需手动上报任务完成。

```php
<?php
namespace Imi\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

/**
 * @Cron(id="CronRandomWorker", second="3n", type="random_worker")
 */
class CronRandomWorker implements ICronTask
{
    /**
     * 执行任务
     *
     * @param string $id
     * @param mixed $data
     * @return void
     */
    public function run(string $id, $data)
    {
        var_dump('random');
    }

}
```

### 定时规则

支持注解设定和配置文件设定两种模式，其中配置文件设定，是可以覆盖注解设定的。

#### 注解设定

注解 `@Cron`，类 `Imi\Cron\Annotation\Cron`

`@Cron(id="任务唯一ID", type="", year="", month="", day="", hour="", minute="", second="", unique=null, redisPool="", lockWaitTimeout="", maxExecutionTime="", force=false, delayMin=0, delayMax=0)`

##### 属性

**id**

使用`@Cron`注解时的任务唯一ID。如果是 `Task`、`Process`，默认使用 `Task` 或 `Process` + 名称。

**type**

任务类型

可选：

`random_worker`-随机工作进程任务

`all_worker`-所有工作进程执行的任务

`task`-后台任务

`process`-进程

`cron_process`-定时任务进程

**force**

每次启动服务强制执行，默认为`false`

**year**

指定任务执行年份，默认为 `*`。

`*` - 不限制

`2019` - 指定年

`2019-2022` - 指定年份区间

`2019,2021,2022` - 指定多个年份

`2n` - 每 2 年，其它以此类推

**month**

指定任务执行月份，默认为 `*`。

`*` - 不限制

`1` (1 月), `-1` (12 月) - 指定月份，支持负数为倒数的月

`1-6` (1-6 月), `-3--1` (10-12 月) - 指定月份区间，支持负数为倒数的月

`1,3,5,-1` (1、3、5、12 月) - 指定多个月份，支持负数为倒数的月

`2n` - 每 2 个月，其它以此类推

**day**

指定任务执行日期，默认为 `*`。

`*` - 不限制

`1` (1 日), `-1` (每月最后一天) - 指定日期，支持负数为倒数的日期

`1-6` (1-6 日), `-3--1` (每月倒数 3 天) - 指定日期区间，支持负数为倒数的日期

`1,3,5,-1` (每月 1、3、5、最后一天) - 指定多个日期，支持负数为倒数的日期

`2n` - 每 2 天，其它以此类推

`year 1` (一年中的第 1 日), `year -1` (每年最后一天) - 指定一年中的日期，支持负数为倒数的日期

`1-6` (一年中的第 1-6 日), `-3--1` (每年倒数 3 天) - 指定一年中的日期区间，支持负数为倒数的日期

`year 1-6` (一年中的第 1-6 日), `year -3--1` (每年倒数 3 天) - 指定一年中的日期区间，支持负数为倒数的日期

`1,3,5,-1` (每年 1、3、5、最后一天) - 指定一年中的多个日期，支持负数为倒数的日期

`year 1,3,5,-1` (每年 1、3、5、最后一天) - 指定一年中的多个日期，支持负数为倒数的日期

**week**

指定周几执行任务，默认为 `*`。
`*` - 不限制
`1` (周一), `-1` (周日) - 指定周几（1-7），支持负数为倒数的周
`1-6` (周一到周六), `-3--1` (周五到周日) - 指定周几，支持负数为倒数的周
`1,3,5,-1` (周一、三、五、日) - 指定多个日期，支持负数为倒数的周

**hour**

指定任务执行小时，默认为 `*`。

`*` - 不限制

`0` (0 点), `-1` (23 点) - 指定小时，支持负数为倒数的小时

`1-6` (1-6 店), `-3--1` (21-23 点) - 指定小时区间，支持负数为倒数的小时

`1,3,5,-1` (1、3、5、23 点) - 指定多个小时，支持负数为倒数的小时

`2n` - 每 2 小时，其它以此类推

**minute**

指定任务执行分钟，默认为 `*`。

`*` - 不限制

`0` (0 分), `-1` (59 分) - 指定分钟，支持负数为倒数的分钟

`1-6` (1-6 分), `-3--1` (57-59 分) - 指定分钟区间，支持负数为倒数的分钟

`1,3,5,-1` (1、3、5、59 分) - 指定多个分钟，支持负数为倒数的分钟

`2n` - 每 2 分钟，其它以此类推

**second**

指定任务执行秒，默认为 `*`。

`*` - 不限制

`0` (0 秒), `-1` (59 秒) - 指定秒，支持负数为倒数的秒

`1-6` (1-6 秒), `-3--1` (57-59 秒) - 指定秒区间，支持负数为倒数的秒

`1,3,5,-1` (1、3、5、59 秒) - 指定多个秒，支持负数为倒数的秒

`2n` - 每 2 秒，其它以此类推

**unique**

定时任务唯一性设置
当前实例唯一: current
所有实例唯一: all
不唯一: null

**redisPool**

用于锁的 `Redis` 连接池名

**lockWaitTimeout**

获取锁超时时间，单位：秒

**maxExecutionTime**

最大运行执行时间，单位：秒。

该值与分布式锁超时时间共享，默认为 60 秒

**delayMin、delayMax**

最小、最大延迟执行秒数，默认为`0`。

如果有一项不为`0`，该定时任务就会根据两个值之间的随机秒数（包含两个值），提前或者延后执行。

这两个设置主要是防止固定时间执行任务过多，起到分流作用。

#### 配置文件设定

项目配置文件，`beans` 节中配置

```php
[
    'CronManager'   =>  [
        // 启用任务进程终端输出
        'stdOutput' => true,
        // 任务列表定义
        'tasks' =>  [
            // 任务唯一ID
            'taskName'  =>  [
                // 任务类型，可选：worker-工作进程任务; task-任务; process-进程
                'type'      =>  'worker',
                // 任务执行回调，可以是callable类型，也可以是 task、process 名
                'task'      =>  mixed,
                // 定时配置
                'cron'     =>  [
                    // 支持多个条件去触发
                    // 规则同 @Cron 注解
                    [
                        'year'  =>  '',
                        'month' =>  '',
                        'day'   =>  '',
                        'week'  =>  '',
                        'hour'  =>  '',
                        'minute'=>  '',
                        'second'=>  '',
                    ],
                ],
                // 可选配置
                'data'              =>  null,
                'unique'            =>  null,
                'redisPool'         =>  'redis',
                'lockWaitTimeout'   =>  10,
                'maxExecutionTime'  =>  120,
                'force'             =>  false,
            ],
        ],
    ],
]
```

### 动态维护

#### 增加定时任务

```php
use Imi\Cron\Util\CronUtil;
use Imi\Cron\Annotation\Cron;
use Imi\Test\HttpServer\Cron\CronDWorker;

$cron = new Cron;
$cron->id = 'CronRandomWorkerTest';
$cron->second = '3n';
$cron->type = 'random_worker';
CronUtil::addCron($cron, CronDWorker::class);
```

#### 移除定时任务

```php
use Imi\Cron\Util\CronUtil;

CronUtil::removeCron('任务ID');
```

#### 移除所有任务

```php
use Imi\Cron\Util\CronUtil;

CronUtil::clear();
```

#### 检测是否存在任务

```php
use Imi\Cron\Util\CronUtil;

CronUtil::hasTask('任务ID');
```



#### 获取所有任务

```php
use Imi\Cron\Util\CronUtil;

$realTasksMsg = CronUtil::getRealTasks();
$realTasks = $realTasksMsg->response;

foreach ($realTasks as $taskName => $task) {
    echo "任务#$taskName : $task->id";
}
```

注：通过getRealTasks可以获得

| 参数             | 类型                   | 说明                 |
| ---------------- | ---------------------- | -------------------- |
| id               | string                 | 任务ID               |
| type             | string                 | 任务类型             |
| task             | string                 | 任务类               |
| cronRules        | array[CronRuleOjbect]  | 运行规则             |
| data             | array                  | 运行参数             |
| unique           | ['current','all',null] | 唯一性设置           |
| redisPool        | string                 | 锁连接池名           |
| lockWaitTimeout  | float                  | 锁超时时间           |
| maxExecutionTime | float                  | 最大运行执行时间     |
| lastRunTime      | int                    | 最近执行时间戳       |
| force            | bool                   | 是否启动服务强制执行 |
