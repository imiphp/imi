# 事件监听

[toc]

## 全局事件

### 事件监听

#### 注解监听

以`imi/src/Listener/Init.php`为例

```php
<?php
namespace Imi\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.INITED",priority=PHP_INT_MAX)
 */
class Init implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // ...
    }
}
```

首先需要定义一个类，类名和位置无所谓。

类必须实现`IEventListener`接口和`public function handle(EventParam $e): void`方法。

然后在类上写`@Listener`注解。注解有两个参数：

`eventName`要监听的事件名称
`priority`事件触发后执行的优先级，数字越大越先执行，同样大执行顺序不一定

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

Event::trigger('事件名称', '传入事件回调的数据', '目标对象', '参数类，默认为EventParam::class');
```

## 局部事件

局部事件就是在某个类实例的事件。

### 事件监听

#### 注解监听

以`imi/src/Server/Http/Listener/BeforeRequest.php`为例

```php
<?php
namespace Imi\Swoole\Server\Http\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Listener\IRequestEventListener;

/**
 * request事件前置处理
 * @ClassEventListener(className="Imi\Swoole\Server\Http\Server",eventName="request",priority=PHP_INT_MAX)
 */
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

然后在类上写`@ClassEventListener`注解。注解参数如下：

`className`类名
`eventName`要监听的事件名称
`priority`事件触发后执行的优先级，数字越大越先执行，同样大执行顺序不一定

#### 代码监听

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

### 自定义事件

```php
$object->trigger('事件名称', '传入事件回调的数据', '目标对象', '参数类，默认为EventParam::class');
```
