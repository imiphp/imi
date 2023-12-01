# Workerman 事件列表

[toc]

## Workerman Server 全局事件

### IMI.WORKERMAN.SERVER.BUFFER_DRAIN

onBufferDrain

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_BUFFER_DRAIN`

**事件类：** `Imi\Workerman\Server\Event\ServerBufferDrainEvent`

### IMI.WORKERMAN.SERVER.BUFFER_FULL

onBufferFull

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_BUFFER_FULL`

**事件类：** `Imi\Workerman\Server\Event\ServerBufferFullEvent`

### IMI.WORKERMAN.SERVER.CLOSE

onClose

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_CLOSE`

**事件类：** `Imi\Workerman\Server\Event\WorkermanConnectionCloseEvent`

### IMI.WORKERMAN.SERVER.CONNECT

onConnect

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_CONNECT`

**事件类：** `Imi\Workerman\Server\Event\ConnectEvent`

### IMI.WORKERMAN.SERVER.ERROR

onError

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_ERROR`

**事件类：** `Imi\Workerman\Server\Event\WorkermanErrorEvent`

### IMI.WORKERMAN.SERVER.WORKER_RELOAD

onWorkerReload

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WORKER_RELOAD`

**事件类：** `Imi\Workerman\Server\Event\WorkerReloadEvent`

### IMI.WORKERMAN.SERVER.WORKER_START

onWorkerStart

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WORKER_START`

**事件类：** `Imi\Workerman\Server\Event\WorkerStartEvent`

### IMI.WORKERMAN.SERVER.WORKER_STOP

onWorkerStop

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WORKER_STOP`

**事件类：** `Imi\Workerman\Server\Event\WorkerStopEvent`

### IMI.WORKERMAN.SERVER.HTTP.REQUEST

http onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_HTTP_REQUEST`

**事件类：** `Imi\Workerman\Server\Http\Event\WorkermanHttpRequestEvent`

### IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT

websocket onWebSocketConnect

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WEBSOCKET_CONNECT`

**事件类：** `Imi\Workerman\Server\WebSocket\Event\WebSocketConnectEvent`

### IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE

websocket onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_WEBSOCKET_MESSAGE`

**事件类：** `Imi\Workerman\Server\WebSocket\Event\WorkermanWebSocketMessageEvent`

### IMI.WORKERMAN.SERVER.TCP.MESSAGE

tcp onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_TCP_MESSAGE`

**事件类：** `Imi\Workerman\Server\Tcp\Event\WorkermanTcpMessageEvent`

### IMI.WORKERMAN.SERVER.UDP.MESSAGE

udp onMessage

**常量：** `Imi\Workerman\Event\WorkermanEvents::SERVER_UDP_MESSAGE`

**事件类：** `Imi\Workerman\Server\Udp\Event\WorkermanUdpMessageEvent`
