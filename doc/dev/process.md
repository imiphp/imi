# 进程工具

## 启动进程

开启一个进程，可以任意添加参数

必选参数：
`-name` 进程名称，通过`@Process`注解定义

可选参数：
`-redirectStdinStdout` 重定向子进程的标准输入和输出。启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取。
`-pipeType` 管道类型，启用$redirectStdinStdout后，此选项将忽略用户参数，强制为1。如果子进程内没有进程间通信，可以设置为 0

示例：

```
HttpDemo/bin/imi process/start -name 进程名称

# 跟上进程需要获取的参数
HttpDemo/bin/imi process/start -name 进程名称 -a 1 -b 2
```

## 启动进程池

基于`Swoole\Process\Pool`实现，可以设定进程数量。需要在进程中人为写上循环，否则进程一旦结束，会立即拉起一个新进程。

必选参数：
`-name` 进程池名称，通过`@ProcessPool`注解定义

可选参数：
`-worker` 进程数量，不传则根据注解配置设定（注解不设置则为1）
`-ipcType` 进程间通信的模式，默认为0表示不使用任何进程间通信特性，不传则根据注解配置设定
`-msgQueueKey` 消息队列键，不传则根据注解配置设定