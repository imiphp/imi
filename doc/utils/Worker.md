# Worker

**类名:** `Imi\Worker`

## 方法

### setWorkerHandler

设置 Worker 的具体实现类

> 例如 Swoole、Workerman 下的实现类各不相同

`public static function setWorkerHandler(\Imi\Contract\IWorker $workerHandler): void`

### getWorkerHandler

获取 Worker 的具体实现类

> 例如 Swoole、Workerman 下的实现类各不相同

`public static function getWorkerHandler(): \Imi\Contract\IWorker`

### getWorkerId

获取当前 worker 进程的 ID

注意，不是进程ID

`public static function getWorkerId(): ?int`

### isInited

是否初始化完毕

`public static function isInited(): bool`

### inited

初始化完毕

`public static function inited(): void`

### getWorkerNum

获取 Worker 进程数量

`public static function getWorkerNum(): int`

### getMasterPid

获取服务器 master 进程 PID

`public static function getMasterPid(): int`
