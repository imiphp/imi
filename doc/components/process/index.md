# 进程

IMI 中对进程的创建和运行做了简单封装。

### 定义进程

```php
<?php
namespace ImiDemo\HttpDemo\MainServer\Process;

use Imi\Process\BaseProcess;
use Imi\Process\Annotation\Process;

/**
 * @Process("tp1")
 */
class TestProcess extends BaseProcess
{
    public function run(\Swoole\Process $process)
    {
        var_dump($this->data);
        sleep(3);
        var_dump('testProcess');
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

#### @Process

```php
/**
 * 进程名称
 * @var string
 */
public $name;

/**
 * 重定向子进程的标准输入和输出。启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取。
 * @var boolean
 */
public $redirectStdinStdout = false;

/**
 * 管道类型，启用$redirectStdinStdout后，此选项将忽略用户参数，强制为1。如果子进程内没有进程间通信，可以设置为 0
 * @var int
 */
public $pipeType = 2;

/**
 * 自动开启协程
 *
 * @var boolean
 */
public $co = true;
```

### 创建进程

通过注解中的进程名称创建进程，返回`\Swoole\Process`类型的对象，需要手动调用`start()`方法来运行进程。

```php
/**
 * 创建进程
 * 本方法无法在控制器中使用
 * 返回\Swoole\Process对象实例
 * 
 * @param string $name
 * @param array $args
 * @param boolean $redirectStdinStdout
 * @param int $pipeType
 * @return \Swoole\Process
 */
ProcessManager::create($name, $args = [], $redirectStdinStdout = null, $pipeType = null): \Swoole\Process
```

### 运行进程，同步阻塞等待进程执行返回

```php
/**
 * 运行进程，同步阻塞等待进程执行返回
 * 不返回\Swoole\Process对象实例
 * 执行失败返回false，执行成功返回数组，包含了进程退出的状态码、信号、输出内容。
 * array(
 * 	'code' => 0,
 * 	'signal' => 0,
 * 	'output' => '',
 * );
 *
 * @param string $name
 * @param array $args
 * @param boolean $redirectStdinStdout
 * @param int $pipeType
 * @return array
 */
ProcessManager::run($name, $args = [], $redirectStdinStdout = null, $pipeType = null)
```

### 运行进程，创建一个协程执行进程，无法获取进程执行结果

```php
/**
 * 运行进程，创建一个协程执行进程，无法获取进程执行结果
 * 执行失败返回false，执行成功返回数组，包含了进程退出的状态码、信号、输出内容。
 * array(
 * 	'code' => 0,
 * 	'signal' => 0,
 * 	'output' => '',
 * );
 *
 * @param string $name
 * @param array $args
 * @param boolean $redirectStdinStdout
 * @param int $pipeType
 * @return void
 */
ProcessManager::coRun($name, $args = [], $redirectStdinStdout = null, $pipeType = null)
```

### 运行进程，托管到 Manager 进程

> 这个用法只能在IMI.SERVERS.CREATE.AFTER事件中使用！

```php
/**
 * 挂靠Manager进程运行进程
 *
 * @param string $name
 * @param array $args
 * @param boolean $redirectStdinStdout
 * @param int $pipeType
 * @return void
 */
public static function runWithManager($name, $args = [], $redirectStdinStdout = null, $pipeType = null)
```

### 进程随服务启动

在项目配置文件中配置`beans`节：

```php
[
    'AutoRunProcessManager' =>  [
        'processes' =>  [
            // 进程类名或Bean名称
            'XXXProcess',
            // 指定别名 A 为指定进程 XXXProcess，并指定参数
            'A' =>  [
                'process'   =>  'XXXProcess',
                'args'      =>  ['id' => 123],
            ],
        ],
    ],
]
```

### 获取随服务启动的进程对象

```php
use \Imi\Process\ProcessManager;
/** @var \Swoole\Process $process */
$process = ProcessManager::getProcessWithManager('processName');
```
