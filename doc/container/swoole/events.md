# Swoole 事件列表

[toc]

## Swoole 环境事件

### imi.swoole.main_coroutine.end

Swoole 主协程执行完毕事件，此事件在协程中

**常量：** `Imi\Swoole\Event\SwooleEvents::MAIN_COROUTINE_END`

### imi.swoole.main_coroutine.after

Swoole 主协程执行完毕后置事件，此事件在非协程中

**常量：** `Imi\Swoole\Event\SwooleEvents::MAIN_COROUTINE_AFTER`

### imi.swoole.server.before_start

Swoole 服务器开始前

**常量：** `Imi\Swoole\Event\SwooleEvents::BEFORE_SERVER_START`

### imi.main_server.worker.start.app

在项目中监听 WorkerStart 事件

**常量：** `Imi\Swoole\Event\SwooleEvents::WORKER_APP_START`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerStartEventParam`

### imi.process_pool.process.begin

自定义进程池中的进程开始事件

**常量：** `Imi\Swoole\Event\SwooleEvents::PROCESS_POOL_PROCESS_BEGIN`

**事件类：** `Imi\Swoole\Process\Pool\ProcessPoolProcessBegin`

### imi.process_pool.process.end

自定义进程池中的进程结束事件

**常量：** `Imi\Swoole\Event\SwooleEvents::PROCESS_POOL_PROCESS_END`

**事件类：** `Imi\Swoole\Process\Pool\ProcessPoolProcessEnd`

## Swoole Server 全局事件

### imi.main_server.start

OnStart

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_START`

**事件类：** `Imi\Swoole\Server\Event\Param\StartEventParam`

### imi.main_server.shutdown

OnShutdown

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_SHUTDOWN`

**事件类：** `Imi\Swoole\Server\Event\Param\ShutdownEventParam`

### imi.main_server.worker.start

OnWorkerStart

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_START`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerStartEventParam`

### imi.main_server.worker.stop

OnWorkerStop

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_STOP`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerStopEventParam`

### imi.main_server.manager.start

OnManagerStart

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_MANAGER_START`

**事件类：** `Imi\Swoole\Server\Event\Param\ManagerStartEventParam`

### imi.main_server.manager.stop

OnManagerStop

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_MANAGER_STOP`

**事件类：** `Imi\Swoole\Server\Event\Param\ManagerStopEventParam`

### imi.main_server.task

OnTask

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_TASK`

**事件类：** `Imi\Swoole\Server\Event\Param\TaskEventParam`

### imi.main_server.finish

OnFinish

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_FINISH`

**事件类：** `Imi\Swoole\Server\Event\Param\FinishEventParam`

### imi.main_server.pipe_message

OnPipeMessage

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_PIPE_MESSAGE`

**事件类：** `Imi\Swoole\Server\Event\Param\PipeMessageEventParam`

### imi.main_server.worker_error

OnWorkerError

**常量：** `Imi\Swoole\Event\SwooleEvents::SERVER_WORKER_ERROR`

**事件类：** `Imi\Swoole\Server\Event\Param\WorkerErrorEventParam`

### imi.main_server.worker.exit

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

**事件名：**`handshake`

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
