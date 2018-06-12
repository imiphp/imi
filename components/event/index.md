## 全局事件

### 事件监听

#### 注解监听

以`imi/src/Listener/Init.php`为例

```
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

首先需要定义一个类，类名和位置无所谓，只要配置了`beanScan`能被扫描到即可。

类必须实现`IEventListener`接口和`public function handle(EventParam $e)`方法。

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
```

### 自定义事件

## 局部事件

局部事件就是在某个类实例的事件。

### 事件监听

#### 注解监听

#### 代码监听

### 自定义事件