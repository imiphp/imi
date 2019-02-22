# 事件

## imi 框架事件

### IMI.INITED

框架初始化事件

事件参数：

无

### IMI.APP.INIT

项目初始化事件

事件参数：

无

### IMI.MAIN_SERVER.WORKER.START.APP

在项目中监听 WorkerStart 事件

事件参数：

```php
class WorkerStartEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerID;
}
```

### IMI.SERVER.CREATE.BEFORE

创建服务器对象前置操作，主服务器+子服务器，每个创建前都会触发！

事件参数：

无

### IMI.SERVER.CREATE.AFTER

创建服务器对象前置后作，主服务器+子服务器，每个创建后都会触发！

事件参数：

无

### IMI.SERVERS.CREATE.BEFORE

创建服务器对象前置后作，只会触发一次！

事件参数：

无

### IMI.SERVERS.CREATE.AFTER

创建服务器对象前置后作，只会触发一次！

事件参数：

无

### IMI.INIT.WORKER.BEFORE

Worker 进程初始化后置

事件参数：

无

### IMI.INIT.WORKER.AFTER

Worker 进程初始化后置

事件参数：

无

### IMI.PROCESS.BEGIN

自定义进程开始事件

事件参数：

```php
string $name, \Swoole\Process $process
```

### IMI.PROCESS.END

自定义进程结束事件

事件参数：

```php
string $name, \Swoole\Process $process
```

### IMI.PROCESS_POOL.PROCESS.BEGIN

自定义进程池中的进程开始事件

事件参数：

```php
string $name, \Swoole\Process\Pool $pool, int $workerId, int $workerNum, array $args, int $ipcType, string $msgQueueKey
```

### IMI.PROCESS_POOL.PROCESS.END

自定义进程池中的进程结束事件

事件参数：

```php
string $name, \Swoole\Process\Pool $pool, int $workerId, int $workerNum, array $args, int $ipcType, string $msgQueueKey
```

### IMI.SERVER.GROUP.JOIN

服务器逻辑分组加入事件

事件参数：

```php
\Imi\Server\Base $server, string $groupName, int $fd
```

### IMI.SERVER.GROUP.LEAVE

服务器逻辑分组离开事件

事件参数：

```php
\Imi\Server\Base $server, string $groupName, int $fd
```

### IMI.REQUEST_CONTENT.CREATE

请求上下文创建事件

事件参数：无

### IMI.REQUEST_CONTENT.DESTROY

请求上下文销毁事件

事件参数：无

## Swoole Server 全局事件

### IMI.MAIN_SERVER.START

OnStart

事件参数：

```php
class StartEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

}
```

### IMI.MAIN_SERVER.SHUTDOWN

OnShutdown

事件参数：

```php
class ShutdownEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

}
```

### IMI.MAIN_SERVER.WORKER.START

OnWorkerStart

事件参数：

```php
class WorkerStartEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerID;
}
```

### IMI.MAIN_SERVER.WORKER.STOP

OnWorkerStop

事件参数：

```php
class WorkerStopEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerID;
}
```

### IMI.MAIN_SERVER.MANAGER.START

OnManagerStart

事件参数：

```php
class ManagerStartEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

}
```

### IMI.MAIN_SERVER.MANAGER.STOP

OnManagerStop

事件参数：

```php
class ManagerStopEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

}
```

### IMI.MAIN_SERVER.TASK

OnTask

事件参数：

```php
class TaskEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 任务ID
     * @var int
     */
    public $taskID;

    /**
     * worker进程ID
     * @var int
     */
    public $workerID;

    /**
     * 任务数据
     * @var mixed
     */
    public $data;
}
```

### IMI.MAIN_SERVER.FINISH

OnFinish

事件参数：

```php
class FinishEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 任务ID
     * @var int
     */
    public $taskID;

    /**
     * 任务数据
     * @var mixed
     */
    public $data;
}
```

### IMI.MAIN_SERVER.PIPE_MESSAGE

OnPipeMessage

事件参数：

```php
class PipeMessageEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerID;

    /**
     * 消息内容，可以是任意PHP类型
     * @var mixed
     */
    public $message;
}
```

### IMI.MAIN_SERVER.WORKER_ERROR

OnWorkerError

事件参数：

```php
class WorkerErrorEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerID;

    /**
     * Worker进程PID
     *
     * @var int
     */
    public $workerPid;
    
    /**
     * 退出的状态码，范围是 1 ～255
     *
     * @var int
     */
    public $exitCode;

    /**
     * 进程退出的信号
     *
     * @var int
     */
    public $signal;
}
```

## Swoole Server 对象事件

对象事件是由多个服务器对象发出的，监听多个端口即认定为多个服务器。

### Http Server

#### request

事件参数：

```php
class RequestEventParam extends EventParam
{
    /**
     * swoole 请求对象
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;

    /**
     * swoole 响应对象
     * @var \Imi\Server\Http\Message\Response
     */
    public $response;
}
```

#### close

事件参数：

```php
class CloseEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;

    /**
     * 来自那个reactor线程
     *
     * @var int
     */
    public $reactorID;
}
```

### WebSocket Server

#### handShake

事件参数：

```php
class HandShakeEventParam extends EventParam
{
    /**
     * swoole 请求对象
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;

    /**
     * swoole 响应对象
     * @var \Imi\Server\Http\Message\Response
     */
    public $response;
}
```

#### message

事件参数：

```php
class MessageEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * swoole 数据帧对象
     * @var \swoole_websocket_frame
     */
    public $frame;
}
```

#### close

事件参数：

```php
class CloseEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;

    /**
     * 来自那个reactor线程
     *
     * @var int
     */
    public $reactorID;
}
```

### TCP Server

#### connect

事件参数：

```php
class ConnectEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;

    /**
     * Reactor线程ID
     * @var int
     */
    public $reactorID;
}
```

#### receive

事件参数：

```php
class ReceiveEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;

    /**
     * Reactor线程ID
     * @var int
     */
    public $reactorID;

    /**
     * 接收到的数据
     *
     * @var string
     */
    public $data;
}
```

#### close

事件参数：

```php
class CloseEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;

    /**
     * 来自那个reactor线程
     *
     * @var int
     */
    public $reactorID;
}
```

#### BufferFull

事件参数：

```php
class BufferEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;
}
```

#### BufferEmpty

事件参数：

```php
class BufferEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $fd;
}
```

### UDP Server

#### packet

```php
class PacketEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 数据
     * @var string
     */
    public $data;

    /**
     * 客户端信息
     *
     * @var array
     */
    public $clientInfo;
}
```