# 事件列表

[toc]

## imi 框架事件

### IMI.LOAD_CONFIG

加载配置

**常量：** `Imi\Core\CoreEvents::LOAD_CONFIG`

### IMI.INITED

框架初始化事件

**常量：** `Imi\Core\CoreEvents::INITED`

### IMI.APP_RUN

应用运行事件

**常量：** `Imi\Core\CoreEvents::APP_RUN`

### IMI.APP.INIT

项目初始化事件，执行比 `IMI.APP_RUN` 要晚

**常量：** `Imi\Core\CoreEvents::APP_INIT`

### IMI.COMMAND.BEFORE

命令行执行前置事件

**常量：** `Imi\Cli\Event\CommandEvents::BEFORE_COMMAND`

### IMI.COMMAND.AFTER

命令行执行后置事件

**常量：** `Imi\Cli\Event\CommandEvents::AFTER_COMMAND`

### IMI.LOAD_RUNTIME

加载运行时

**常量：** `Imi\Core\CoreEvents::LOAD_RUNTIME`

### IMI.LOAD_RUNTIME_INFO

加载 runtime 事件，在此事件中，绝对可以使用 `App::getRuntimeInfo()` 获取到数据。

**常量：** `Imi\Core\CoreEvents::LOAD_RUNTIME_INFO`

**事件类：** `Imi\Core\Runtime\Event\LoadRuntimeInfoEvent`

### IMI.BUILD_RUNTIME

构建运行时缓存

**常量：** `Imi\Core\CoreEvents::BUILD_RUNTIME`

**事件类：** `Imi\Core\Runtime\Event\BuildRuntimeInfoEvent`

### IMI.SERVER.CREATE.BEFORE

创建服务器对象前置操作，主服务器+子服务器，每个创建前都会触发！

**常量：** `Imi\Server\ServerEvents::BEFORE_CREATE_SERVER`

**事件类：** `Imi\Server\Event\BeforeCreateServerEvent`

### IMI.SERVER.CREATE.AFTER

创建服务器对象后置操作，主服务器+子服务器，每个创建后都会触发！

**常量：** `Imi\Server\ServerEvents::AFTER_CREATE_SERVER`

**事件类：** `Imi\Server\Event\AfterCreateServerEvent`

### IMI.SERVERS.CREATE.BEFORE

创建服务器前置操作，只会触发一次！

**常量：** `Imi\Server\ServerEvents::BEFORE_CREATE_SERVERS`

### IMI.SERVERS.CREATE.AFTER

创建服务器后置操作，只会触发一次！

**常量：** `Imi\Server\ServerEvents::AFTER_CREATE_SERVERS`

### IMI.SERVER.WORKER_START

服务器 worker 进程启动事件

> 此事件常驻内存容器下，都会触发调用

**常量：** `Imi\Server\ServerEvents::WORKER_START`

**事件类：** `Imi\Server\Event\WorkerStartEvent`

### IMI.SERVER.WORKER_STOP

服务器 worker 进程停止事件

> 此事件常驻内存容器下，都会触发调用

**常量：** `Imi\Server\ServerEvents::WORKER_STOP`

**事件类：** `Imi\Server\Event\WorkerStopEvent`

### IMI.PROCESS.BEGIN

自定义进程开始事件

**常量：** `Imi\Process\Event\ProcessEvents::PROCESS_BEGIN`

**事件类：** `Imi\Process\Event\ProcessBeginEvent`

### IMI.PROCESS.END

自定义进程结束事件

**常量：** `Imi\Process\Event\ProcessEvents::PROCESS_END`

**事件类：** `Imi\Process\Event\ProcessEndEvent`

### IMI.SERVER.GROUP.JOIN

服务器逻辑分组加入事件

**常量：** `Imi\Server\Group\Event\ServerGroupEvents::JOIN_GROUP`

**事件类：** `Imi\Server\Group\Event\JoinGroupEvent`

### IMI.SERVER.GROUP.LEAVE

服务器逻辑分组离开事件

**常量：** `Imi\Server\Group\Event\ServerGroupEvents::LEAVE_GROUP`

**事件类：** `Imi\Server\Group\Event\LeaveGroupEvent`

### IMI.QUICK_START_BEFORE

快速启动前置事件

**常量：** `Imi\Core\CoreEvents::BEFORE_QUICK_START`

### IMI.QUICK_START_AFTER

快速启动后置事件

**常量：** `Imi\Core\CoreEvents::AFTER_QUICK_START`

### IMI.SCAN_IMI

扫描框架

**常量：** `Imi\Core\CoreEvents::SCAN_IMI`

### IMI.SCAN_VENDOR

扫描 vendor 组件

**常量：** `Imi\Core\CoreEvents::SCAN_VENDOR`

### IMI.SCAN_APP

扫描项目

**常量：** `Imi\Core\CoreEvents::SCAN_APP`

### IMI.INIT_MAIN

初始化 Main 类

**常量：** `Imi\Core\CoreEvents::INIT_MAIN`
