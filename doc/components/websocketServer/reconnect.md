# 断线重连

使用 WebSocket、Http2、TCP 等长连接协议时，很多场景会需要断线重连功能。

我们使用一个标记（UID），来绑定连接。

断线重连就是连接断开后，该连接的上下文数据会继续保留，在一定时间内重连，就可以恢复上下文数据。

一般用于游戏重连、减少重连后的网络通信、减轻前端开发压力等场景。

> 断线重连视频介绍：<https://www.bilibili.com/video/BV1GC4y1s7yf>

使用文档详见：<https://doc.imiphp.com/components/websocketServer/session.html>
