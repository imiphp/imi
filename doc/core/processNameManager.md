# 进程名称管理

[toc]

imi 为启动出来的进程统一管理了进程名，你可以在 `@app.process` 中自定义，其默认设置为：

**Swoole：**

```php
'process'   =>  [
    'master'        =>  'imi:master:{namespace}',
    'manager'       =>  'imi:manager:{namespace}',
    'worker'        =>  'imi:worker-{workerId}:{namespace}',
    'taskWorker'    =>  'imi:taskWorker-{workerId}:{namespace}',
    'process'       =>  'imi:process-{processName}:{namespace}',
    'processPool'   =>  'imi:process-pool-{processPoolName}-{workerId}:{namespace}',
]
```

**Workerman：**

```php
'process'   =>  [
    'master'        =>  'imi:master:{namespace}',
    'worker'        =>  'imi:worker-{workerId}:{namespace}',
    'process'       =>  'imi:process-{processName}:{namespace}',
]
```
