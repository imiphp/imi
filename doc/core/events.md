# 事件列表

## imi 框架事件

### IMI.INITED

框架初始化事件

事件参数：

无

### IMI.APP.INIT

项目初始化事件

事件参数：

无

### IMI.COMMAND.BEFORE

命令行执行前置事件

事件参数：

无

### IMI.COMMAND.AFTER

命令行执行后置事件

事件参数：

无

### IMI.LOAD_RUNTIME_INFO

加载 runtime 事件，在此事件中，绝对可以使用 `App::getRuntimeInfo()` 获取到数据。

### IMI.SERVER.CREATE.BEFORE

创建服务器对象前置操作，主服务器+子服务器，每个创建前都会触发！

事件参数：

无

### IMI.SERVER.CREATE.AFTER

创建服务器对象前置后作，主服务器+子服务器，每个创建后都会触发！

事件参数：

无

### IMI.SERVERS.CREATE.BEFORE

创建服务器对象前置后作，只会触发一次！

事件参数：

无

### IMI.SERVERS.CREATE.AFTER

创建服务器对象前置后作，只会触发一次！

事件参数：

无

### IMI.INIT.WORKER.BEFORE

Worker 进程初始化后置

事件参数：

无

### IMI.INIT.WORKER.AFTER

Worker 进程初始化后置

事件参数：

无

### IMI.PROCESS.BEGIN

自定义进程开始事件

事件参数：

```php
string $name, $process
```

### IMI.PROCESS.END

自定义进程结束事件

事件参数：

```php
string $name, $process
```

### IMI.SERVER.GROUP.JOIN

服务器逻辑分组加入事件

事件参数：

```php
\Imi\Server\Contract\IServer $server, string $groupName, $clientId
```

### IMI.SERVER.GROUP.LEAVE

服务器逻辑分组离开事件

事件参数：

```php
\Imi\Server\Contract\IServer $server, string $groupName, $clientId
```

### IMI.REQUEST_CONTENT.CREATE

请求上下文创建事件

事件参数：无

### IMI.REQUEST_CONTENT.DESTROY

请求上下文销毁事件

事件参数：无
