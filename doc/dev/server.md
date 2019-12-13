# 服务器工具

## 开启服务

无参数

示例：

```
HttpDemo/bin/imi server/start
```

## 停止服务

无参数

示例：

```
HttpDemo/bin/imi server/stop
```

## 重新加载服务

重启 Worker 进程，不会导致连接断开，可以让项目文件更改生效

无参数

示例：

```
HttpDemo/bin/imi server/reload
```

更新运行时缓存后，再重新加载服务

```
HttpDemo/bin/imi server/reload -runtime
```