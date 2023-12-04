# 事件列表

[toc]

## imi 框架事件

### imi.load_config

加载配置

**常量：** `Imi\Core\CoreEvents::LOAD_CONFIG`

### imi.inited

框架初始化事件

**常量：** `Imi\Core\CoreEvents::INITED`

### imi.app_run

应用运行事件

**常量：** `Imi\Core\CoreEvents::APP_RUN`

### imi.app.init

项目初始化事件，执行比 `imi.app_run` 要晚

**常量：** `Imi\Core\CoreEvents::APP_INIT`

### imi.command.before

命令行执行前置事件

**常量：** `Imi\Cli\Event\CommandEvents::BEFORE_COMMAND`

### imi.command.after

命令行执行后置事件

**常量：** `Imi\Cli\Event\CommandEvents::AFTER_COMMAND`

### imi.load_runtime

加载运行时

**常量：** `Imi\Core\CoreEvents::LOAD_RUNTIME`

### imi.load_runtime_info

加载 runtime 事件，在此事件中，绝对可以使用 `App::getRuntimeInfo()` 获取到数据。

**常量：** `Imi\Core\CoreEvents::LOAD_RUNTIME_INFO`

**事件类：** `Imi\Core\Runtime\Event\LoadRuntimeInfoEvent`

### imi.build_runtime

构建运行时缓存

**常量：** `Imi\Core\CoreEvents::BUILD_RUNTIME`

**事件类：** `Imi\Core\Runtime\Event\BuildRuntimeInfoEvent`

### imi.server.create.before

创建服务器对象前置操作，主服务器+子服务器，每个创建前都会触发！

**常量：** `Imi\Server\ServerEvents::BEFORE_CREATE_SERVER`

**事件类：** `Imi\Server\Event\BeforeCreateServerEvent`

### imi.server.create.after

创建服务器对象后置操作，主服务器+子服务器，每个创建后都会触发！

**常量：** `Imi\Server\ServerEvents::AFTER_CREATE_SERVER`

**事件类：** `Imi\Server\Event\AfterCreateServerEvent`

### imi.servers.create.before

创建服务器前置操作，只会触发一次！

**常量：** `Imi\Server\ServerEvents::BEFORE_CREATE_SERVERS`

### imi.servers.create.after

创建服务器后置操作，只会触发一次！

**常量：** `Imi\Server\ServerEvents::AFTER_CREATE_SERVERS`

### imi.server.worker_start

服务器 worker 进程启动事件

> 此事件常驻内存容器下，都会触发调用

**常量：** `Imi\Server\ServerEvents::WORKER_START`

**事件类：** `Imi\Server\Event\WorkerStartEvent`

### imi.server.worker_stop

服务器 worker 进程停止事件

> 此事件常驻内存容器下，都会触发调用

**常量：** `Imi\Server\ServerEvents::WORKER_STOP`

**事件类：** `Imi\Server\Event\WorkerStopEvent`

### imi.process.begin

自定义进程开始事件

**常量：** `Imi\Process\Event\ProcessEvents::PROCESS_BEGIN`

**事件类：** `Imi\Process\Event\ProcessBeginEvent`

### imi.process.end

自定义进程结束事件

**常量：** `Imi\Process\Event\ProcessEvents::PROCESS_END`

**事件类：** `Imi\Process\Event\ProcessEndEvent`

### imi.server.group.join

服务器逻辑分组加入事件

**常量：** `Imi\Server\Group\Event\ServerGroupEvents::JOIN_GROUP`

**事件类：** `Imi\Server\Group\Event\JoinGroupEvent`

### imi.server.group.leave

服务器逻辑分组离开事件

**常量：** `Imi\Server\Group\Event\ServerGroupEvents::LEAVE_GROUP`

**事件类：** `Imi\Server\Group\Event\LeaveGroupEvent`

### imi.quick_start_before

快速启动前置事件

**常量：** `Imi\Core\CoreEvents::BEFORE_QUICK_START`

### imi.quick_start_after

快速启动后置事件

**常量：** `Imi\Core\CoreEvents::AFTER_QUICK_START`

### imi.scan_imi

扫描框架

**常量：** `Imi\Core\CoreEvents::SCAN_IMI`

### imi.scan_vendor

扫描 vendor 组件

**常量：** `Imi\Core\CoreEvents::SCAN_VENDOR`

### imi.scan_app

扫描项目

**常量：** `Imi\Core\CoreEvents::SCAN_APP`

### imi.init_main

初始化 Main 类

**常量：** `Imi\Core\CoreEvents::INIT_MAIN`
