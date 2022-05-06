# Workerman 事件列表

[toc]

## Workerman Server 全局事件

### IMI.WORKERMAN.SERVER.BUFFER_DRAIN

onBufferDrain

事件参数：

名称 | 描述
-|-
server|服务器对象
clientId|连接ID
connection|Workerman Connection 对象

### IMI.WORKERMAN.SERVER.BUFFER_FULL

onBufferFull

事件参数：

名称 | 描述
-|-
server|服务器对象
clientId|连接ID
connection|Workerman Connection 对象

### IMI.WORKERMAN.SERVER.CLOSE

onClose

事件参数：

名称 | 描述
-|-
server|服务器对象
clientId|连接ID
connection|Workerman Connection 对象

### IMI.WORKERMAN.SERVER.CONNECT

onConnect

事件参数：

名称 | 描述
-|-
server|服务器对象
clientId|连接ID
connection|Workerman Connection 对象

### IMI.WORKERMAN.SERVER.ERROR

onError

事件参数：

名称 | 描述
-|-
server|服务器对象
clientId|连接ID
connection|Workerman Connection 对象
code|错误代码
msg|错误信息

### IMI.WORKERMAN.SERVER.WORKER_RELOAD

onWorkerReload

事件参数：

名称 | 描述
-|-
server|服务器对象
worker|Workerman Worker 对象

### IMI.WORKERMAN.SERVER.WORKER_START

onWorkerStart

事件参数：

名称 | 描述
-|-
server|服务器对象
worker|Workerman Worker 对象

### IMI.WORKERMAN.SERVER.WORKER_STOP

onWorkerStop

事件参数：

名称 | 描述
-|-
server|服务器对象
worker|Workerman Worker 对象

### IMI.WORKERMAN.SERVER.HTTP.REQUEST

http onMessage

事件参数：

名称 | 描述
-|-
server|服务器对象
request|Request 对象
response|Response 对象

### IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT

websocket onWebSocketConnect

事件参数：

名称 | 描述
-|-
server|服务器对象
connection|Workerman Connection 对象
clientId|连接ID
request|Request 对象
response|Response 对象

### IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE

websocket onMessage

事件参数：

名称 | 描述
-|-
server|服务器对象
connection|Workerman Connection 对象
clientId|连接ID
data|原始数据
frame|`\Imi\Workerman\Cron\Protocol\Frame` 对象

### IMI.WORKERMAN.SERVER.TCP.MESSAGE

tcp onMessage

事件参数：

名称 | 描述
-|-
server|服务器对象
connection|Workerman Connection 对象
clientId|连接ID
data|原始数据

### IMI.WORKERMAN.SERVER.UDP.MESSAGE

udp onMessage

事件参数：

名称 | 描述
-|-
server|服务器对象
connection|Workerman Connection 对象
data|原始数据
packetData|`\Imi\Workerman\Server\Udp\Message\PacketData` 对象
