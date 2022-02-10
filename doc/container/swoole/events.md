# Swoole 事件列表

## Swoole 环境事件

### IMI.SWOOLE.MAIN_COROUTINE.END

Swoole 主协程执行完毕事件，此事件在协程中

事件参数：

无

### IMI.SWOOLE.MAIN_COROUTINE.AFTER

Swoole 主协程执行完毕后置事件，此事件在非协程中

事件参数：

无

### IMI.SWOOLE.SERVER.BEFORE_START

Swoole 服务器开始前

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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerId;
}
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

## Swoole Server 全局事件

### IMI.MAIN_SERVER.START

OnStart

事件参数：

```php
class StartEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
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
     * @var \Imi\Swoole\Server\Base
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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerId;
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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerId;
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
     * @var \Imi\Swoole\Server\Base
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
     * @var \Imi\Swoole\Server\Base
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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 任务ID
     * @var int
     */
    public $taskId;

    /**
     * worker进程ID
     * @var int
     */
    public $workerId;

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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 任务ID
     * @var int
     */
    public $taskId;

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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerId;

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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * Worker进程ID
     * @var int
     */
    public $workerId;

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

事件监听：`@ClassEventListener(className="Imi\Swoole\Server\Http\Server", eventName="事件名")`

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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;

    /**
     * 来自那个reactor线程
     *
     * @var int
     */
    public $reactorId;
}
```

### WebSocket Server

事件监听：`@ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server", eventName="事件名")`

#### handShake

握手事件

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

#### open

握手完成后打开连接事件

事件参数：

```php
class OpenEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符.
     *
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;
}
```

#### message

事件参数：

```php
class MessageEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;

    /**
     * 来自那个reactor线程
     *
     * @var int
     */
    public $reactorId;
}
```

### TCP Server

事件监听：`@ClassEventListener(className="Imi\Swoole\Server\TcpServer\Server", eventName="事件名")`

#### connect

事件参数：

```php
class ConnectEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;

    /**
     * Reactor线程ID
     * @var int
     */
    public $reactorId;
}
```

#### receive

事件参数：

```php
class ReceiveEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;

    /**
     * Reactor线程ID
     * @var int
     */
    public $reactorId;

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
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;

    /**
     * 来自那个reactor线程
     *
     * @var int
     */
    public $reactorId;
}
```

#### BufferFull

事件参数：

```php
class BufferEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;
}
```

#### BufferEmpty

事件参数：

```php
class BufferEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int
     */
    public $clientId;
}
```

### UDP Server

事件监听：`@ClassEventListener(className="Imi\Swoole\Server\UdpServer\Server", eventName="事件名")`

#### packet

```php
class PacketEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
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
