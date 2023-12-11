# 事件监听

[toc]

imi 框架提供了强大的事件机制，它可以让你在特定的时刻执行某些操作。事件机制的核心是事件触发器和事件监听器。

在 imi 框架中，事件触发器通常是一个具体的操作，而事件监听器则是响应某些事件的具体操作。

事件机制的主要优势在于它可以让你在不改变原有代码的情况下，扩展应用的功能。

imi 基于 PSR-14 基础上做了改造，定义了自己的接口，功能更加强大！

## 全局事件

### 监听全局事件

#### 注解监听全局事件

```php
<?php
namespace Imi\Listener;

use Imi\Event\Contract\IEvent;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

#[Listener(eventName: 'imi.inited', priority: PHP_INT_MAX)]
class Init implements IEventListener
{
    /**
     * 事件处理方法
     */
    public function handle(IEvent $e): void
    {
        // ...
    }
}
```

首先需要定义一个类，类名和位置无所谓。

类必须实现`IEventListener`接口和`public function handle(IEvent $e): void`方法。

然后在类上写`Listener`注解。

**注解参数说明：**

* `eventName` 要监听的事件名称

* `priority` 事件触发后执行的优先级，数字越大越先执行，同样大执行顺序不一定

* `one` 设为 `true` 事件仅触发一次，默认为 `false`

#### 代码监听

除了使用注解，你还可以写代码手动监听

```php
use Imi\Event\Event;

Event::on('事件名', function(){
    // 事件回调
});

$priority = 0;
Event::on('事件名', function(){
    // 事件回调
}, $priority);

// 监听只触发一次
Event::one('事件名', function(){});

// 取消事件
Event::off('事件名', $callable);
```

`on()、one()、off()` 方法还支持传入多个事件：

```php
Event::on(['e1', 'e2'], function(){
    // 事件回调
});
```

取消事件所有监听：

```php
Event::off('事件名');
Event::off(['事件名1', '事件名2']);
```

### 自定义事件

```php
use Imi\Event\Event;
use Imi\Event\CommonEvent;

// 定义事件类
class MyEvent extends CommonEvent
{
    public function __construct(?object $__target = null)
    {
        parent::__construct('事件名称', $__target);
    }
}

// 传入事件对象，不需要指定事件名称
Event::dispatch(new MyEvent());
// 指定事件名称，可不定义事件类
Event::dispatch(eventName: '事件名称');
// 指定事件名称，且传入事件目标对象
Event::dispatch(eventName: '事件名称', target: $this);

// 下面是旧写法，为了保持兼容暂时保留，即将在 3.1 废弃
// Event::trigger('事件名称', '传入事件回调的数据', '目标对象', '参数类，默认为EventParam::class');
```

## 对象事件

对象事件就是在某个类实例的事件。

### 监听对象事件

#### 注解监听对象事件

以`imi/src/Server/Http/Listener/BeforeRequest.php`为例

```php
<?php
namespace Imi\Swoole\Server\Http\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Listener\IRequestEventListener;

/**
 * request事件前置处理
 */
#[ClassEventListener(className: \Imi\Swoole\Server\Http\Server::class, eventName: 'request', priority: PHP_INT_MAX)]
class BeforeRequest implements IRequestEventListener
{
    /**
     * 事件处理方法
     * @param RequestEventParam $e
     * @return void
     */
    public function handle(RequestEventParam $e): void
    {
        // ...
    }
}
```

首先需要定义一个类，类名和位置无所谓。

类必须实现对应接口和`handle()`方法，每个类的事件定义不同。

然后在类上写`ClassEventListener`注解。注解参数如下：

* `className`类名
* `eventName`要监听的事件名称
* `priority`事件触发后执行的优先级，数字越大越先执行，同样大执行顺序不一定

#### 代码监听对象事件

```php
$object->on('事件名', function(){
    // 事件回调
});

$priority = 0;
$object->on('事件名', function(){
    // 事件回调
}, $priority);

// 监听只触发一次
$object->one('事件名', function(){});

// 取消事件
$object->off('事件名', $callable);

```

### 自定义触发事件

```php
$object->trigger('事件名称', '传入事件回调的数据', '目标对象', '参数类，默认为EventParam::class');
```

## 替换事件实现

```php
Imi\App::set(Imi\AppContexts::EVENT_DISPATCHER, Imi\Event\ListenerProvider::class);
Imi\App::set(Imi\AppContexts::EVENT_LISTENER_PROVIDER, Imi\Event\EventDispatcher::class);
```

## 事件名称

事件名称可以是任何字符串，但需要遵循一些命名约定：

* 只使用小写字母、数字、点 (`.`) 和下划线 (`_`)
* 使用命名空间作为名称前缀，后跟一个点（例如 `order.*`、`user.*`）

### 3.0 兼容性

由于 imi < 3.0 的事件名称都是全大写命名的，建议用户在升级到 3.0 时，将事件监听处的事件名称改为小写命名。

如果你暂时不想修改大小写，3.0 也提供了一个临时方案，可以一直使用到 3.1 版本才会被废弃。

在项目入口 `init.php` 或在 `composer.json` 中配置：

```json
{
    "autoload": {
        "files": [
            "init.php"
        ]
    }
}
```

然后在 `init.php` 中设置不区分事件名大小写的事件监听提供者：

```php
Imi\App::set(Imi\AppContexts::EVENT_LISTENER_PROVIDER, Imi\Event\CaseInsensitiveListenerProvider::class);
```
