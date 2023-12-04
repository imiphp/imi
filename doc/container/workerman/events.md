# Workerman 事件列表

[toc]

## Workerman Server 全局事件

### imi.workerman.server.buffer_drain

onBufferDrain

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_BUFFER_DRAIN`

**事件类：** `Imi\Workerman\Server\Event\ServerBufferDrainEvent`

### imi.workerman.server.buffer_full

onBufferFull

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_BUFFER_FULL`

**事件类：** `Imi\Workerman\Server\Event\ServerBufferFullEvent`

### imi.workerman.server.close

onClose

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_CLOSE`

**事件类：** `Imi\Workerman\Server\Event\WorkermanConnectionCloseEvent`

### imi.workerman.server.connect

onConnect

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_CONNECT`

**事件类：** `Imi\Workerman\Server\Event\ConnectEvent`

### imi.workerman.server.error

onError

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_ERROR`

**事件类：** `Imi\Workerman\Server\Event\WorkermanErrorEvent`

### imi.workerman.server.worker_reload

onWorkerReload

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WORKER_RELOAD`

**事件类：** `Imi\Workerman\Server\Event\WorkerReloadEvent`

### imi.workerman.server.worker_start

onWorkerStart

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WORKER_START`

**事件类：** `Imi\Workerman\Server\Event\WorkerStartEvent`

### imi.workerman.server.worker_stop

onWorkerStop

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WORKER_STOP`

**事件类：** `Imi\Workerman\Server\Event\WorkerStopEvent`

### imi.workerman.server.http.request

http onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_HTTP_REQUEST`

**事件类：** `Imi\Workerman\Server\Http\Event\WorkermanHttpRequestEvent`

### imi.workerman.server.websocket.connect

websocket onWebSocketConnect

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WEBSOCKET_CONNECT`

**事件类：** `Imi\Workerman\Server\WebSocket\Event\WebSocketConnectEvent`

### imi.workerman.server.websocket.message

websocket onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WEBSOCKET_MESSAGE`

**事件类：** `Imi\Workerman\Server\WebSocket\Event\WorkermanWebSocketMessageEvent`

### imi.workerman.server.tcp.message

tcp onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_TCP_MESSAGE`

**事件类：** `Imi\Workerman\Server\Tcp\Event\WorkermanTcpMessageEvent`

### imi.workerman.server.udp.message

udp onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_UDP_MESSAGE`

**事件类：** `Imi\Workerman\Server\Udp\Event\WorkermanUdpMessageEvent`
