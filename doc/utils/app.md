# App 类

`Imi\App` 类，是框架应用类。

## 方法

### getNamespace

获取应用命名空间

`public static function getNamespace()`

### getContainer

获取容器对象

`public static function getContainer()`

#### 容器手动绑定

```php
// 绑定
App::getContainer()->bind('aaa', XXX::class);

// 实例化
$obj = App::getBean('aaa');
```

### getBean

获取全局Bean对象

`public static function getBean($name, ...$params)`

### isDebug

当前是否为调试模式

`public static function isDebug()`

### setDebug

开关调试模式

`public static function setDebug($isDebug)`

### get

获取应用上下文数据

`public static function get($name, $default = null)`

### set

设置应用上下文数据

`public static function set($name, $value, $readonly = false)`

第三个参数可以禁止非 `Imi\` 开头的命名空间的类，对应用上下文中`$name`对应的值进行写操作。

#### 框架核心上下文列表

##### 进程相关

类 `Imi\Util\Process\ProcessAppContexts`:

`ProcessAppContexts::PROCESS_TYPE` - 进程类型

`ProcessAppContexts::PROCESS_NAME` - 进程名称

`ProcessAppContexts::MASTER_PID` - 主进程pid

###### 进程类型定义

类 `Imi\Util\Process\ProcessType`:

`ProcessType::MASTER` - master 进程

`ProcessType::MANAGER` - manager 进程

`ProcessType::WORKER` - worker 进程

`ProcessType::TASK_WORKER` - task worker 进程

`ProcessType::PROCESS` - 进程
