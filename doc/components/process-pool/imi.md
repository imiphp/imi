# 进程池-imi

[toc]

imi 实现了一个可以替代 Swoole 进程池的更强大的进程池。支持信号监听、重启进程（可指定）、Pool Master 与 Worker 消息通讯等。

基于 `Swoole\Process` 实现，内部已实现了信号监听。

## 使用

```php
$workerNum = 4;
$processPool = new \Imi\Swoole\Process\Pool($workerNum);
// 初始化事件-可选
$processPool->on('Init', function(\Imi\Swoole\Process\Pool\InitEventParam $e){
    var_dump('init');
});
// 工作进程开始事件-必选
$processPool->on('WorkerStart', function(\Imi\Swoole\Process\Pool\WorkerEventParam $e){
    // 业务代码写这
    while(true)
    {
        // 给 master 进程发消息
        $e->getWorker()->sendMessage('test', [
            'time'  =>  time(),
        ]);
        sleep(3);
    }
});
// 工作进程退出事件-可选
$processPool->on('WorkerExit', function(\Imi\Swoole\Process\Pool\WorkerEventParam $e){
    // 做一些释放操作
});
// 工作进程停止事件-可选
$processPool->on('WorkerStop', function(\Imi\Swoole\Process\Pool\WorkerEventParam $e){

});
// 工作进程接收到消息事件-可选
$processPool->on('Message', function(\Imi\Swoole\Process\Pool\MessageEventParam $e){
    $data = $e->getData();
     // $data['a'] 约定是操作名，其它成员为参数
    switch($data['a'])
    {
        case 'test':
            // 做一些事
            var_dump($e->getWorkerId() . ':' . $data['time']);
            break;
    }
});
$processPool->start(); // 启动
// $processPool->shutdown(); // 停止
```

### 重启进程

重启所有进程：

```php
$processPool->restartAllWorker();
```

重启部分进程：

```php
// 重启 workerId 为 0、3 的进程
$processPool->restartWorker(0, 3);
```
