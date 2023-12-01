# Swoole 事件列表

[toc]

## Swoole 环境事件

### IMI.SWOOLE.MAIN_COROUTINE.END

Swoole 主协程执行完毕事件，此事件在协程中

**常量：** `Imi\Swoole\Event\SwooleEvents::MAIN_COROUTINE_END`

### IMI.SWOOLE.MAIN_COROUTINE.AFTER

Swoole 主协程执行完毕后置事件，此事件在非协程中

**常量：** `Imi\Swoole\Event\SwooleEvents::MAIN_COROUTINE_AFTER`

### IMI.SWOOLE.SERVER.BEFORE_START

Swoole 服务器开始前

**常量：** `Imi\Swoole\Event\SwooleEvents::BEFORE_SERVER_START`

### IMI.MAIN_SERVER.WORKER.START.APP

在项目中监听 WorkerStart 事件

**常量：** `Imi\Swoole\Event\SwooleEvents::WORKER_APP_START`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerStartEventParam`

### IMI.PROCESS_POOL.PROCESS.BEGIN

自定义进程池中的进程开始事件

**常量：** `Imi\Swoole\Event\SwooleEvents::PROCESS_POOL_PROCESS_BEGIN`

**事件类：** `Imi\Swoole\Process\Pool\ProcessPoolProcessBegin`

### IMI.PROCESS_POOL.PROCESS.END

自定义进程池中的进程结束事件

**常量：** `Imi\Swoole\Event\SwooleEvents::PROCESS_POOL_PROCESS_END`

**事件类：** `Imi\Swoole\Process\Pool\ProcessPoolProcessEnd`

## Swoole Server 全局事件

### IMI.MAIN_SERVER.START

OnStart

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_START`

**事件类：** `Imi\Swoole\Server\Event\Param\StartEventParam`

### IMI.MAIN_SERVER.SHUTDOWN

OnShutdown

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_SHUTDOWN`

**事件类：** `Imi\Swoole\Server\Event\Param\ShutdownEventParam`

### IMI.MAIN_SERVER.WORKER.START

OnWorkerStart

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_START`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerStartEventParam`

### IMI.MAIN_SERVER.WORKER.STOP

OnWorkerStop

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_STOP`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerStopEventParam`

### IMI.MAIN_SERVER.MANAGER.START

OnManagerStart

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_MANAGER_START`

**事件类：** `Imi\Swoole\Server\Event\Param\ManagerStartEventParam`

### IMI.MAIN_SERVER.MANAGER.STOP

OnManagerStop

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_MANAGER_STOP`

**事件类：** `Imi\Swoole\Server\Event\Param\ManagerStopEventParam`

### IMI.MAIN_SERVER.TASK

OnTask

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_TASK`

**事件类：** `Imi\Swoole\Server\Event\Param\TaskEventParam`

### IMI.MAIN_SERVER.FINISH

OnFinish

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_FINISH`

**事件类：** `Imi\Swoole\Server\Event\Param\FinishEventParam`

### IMI.MAIN_SERVER.PIPE_MESSAGE

OnPipeMessage

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_PIPE_MESSAGE`

**事件类：** `Imi\Swoole\Server\Event\Param\PipeMessageEventParam`

### IMI.MAIN_SERVER.WORKER_ERROR

OnWorkerError

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_ERROR`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerErrorEventParam`

### IMI.MAIN_SERVER.WORKER.EXIT

OnWorkerError

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_EXIT`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerExitEventParam`

## Swoole Server 对象事件

对象事件是由多个服务器对象发出的，监听多个端口即认定为多个服务器。

### Http Server

事件监听：`#[ClassEventListener(className: "Imi\Swoole\Server\Http\Server", eventName: "事件名")]`

#### Http 请求事件

**事件名：**`request`

**事件参数：**

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

#### Swoole 关闭连接事件

**事件名：**`close`

**事件参数：**

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
     * @var int|string
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

事件监听：`#[ClassEventListener(className: "Imi\Swoole\Server\WebSocket\Server", eventName: "事件名")]`

#### WebSocket 握手事件

**事件名：**`handShake`

握手事件

**事件参数：**

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

#### 握手后打开连接事件

**事件名：**`open`

握手完成后打开连接事件

**事件参数：**

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

#### 消息事件

**事件名：**`message`

**事件参数：**

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

#### WebSocket 连接关闭事件

**事件名：**`close`

**事件参数：**

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
     * @var int|string
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

#### 非 WebSocket 连接关闭事件

**事件名：**`disconnect`

只有非 WebSocket 连接关闭时才会触发该事件。

**事件参数：**

```php
class DisconnectEventParam extends EventParam
{
    /**
     * 服务器对象
     * @var \Imi\Swoole\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符
     * @var int|string
     */
    public $clientId;
}
```

### TCP Server

事件监听：`#[ClassEventListener(className: "Imi\Swoole\Server\TcpServer\Server", eventName: "事件名")]`

#### 连接事件

**事件名：**`connect`

**事件参数：**

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
     * @var int|string
     */
    public $clientId;

    /**
     * Reactor线程ID
     * @var int
     */
    public $reactorId;
}
```

#### 接收数据事件

**事件名：**`receive`

**事件参数：**

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
     * @var int|string
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

#### TCP 连接关闭事件

**事件名：**`close`

**事件参数：**

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
     * @var int|string
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

### UDP Server

事件监听：`#[ClassEventListener(className: "Imi\Swoole\Server\UdpServer\Server", eventName: "事件名")]`

#### 包事件

**事件名：**`packet`

**事件参数：**

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
