## 介绍

首先，你不要看到 AOP 就感觉好难好复杂，看下去其实也就那样。而且在 IMI 中你也不一定需要用到AOP，这是非必须的。

AOP 的概念通过搜索引擎一定是看烦了，而且看了也没什么大卵用，不贴近实际。

我先举个 AOP 实际应用的简单例子，比如在写一个方法的时候，可能要针对某个方法写前置和后置操作，传统写法如下：

```php
abstract class ParentClass
{
	public function test()
	{
		$this->__beforeTest();
		// 做一些事情...
		echo 'Parent->test()', PHP_EOL;
		$this->__afterTest();
	}

	public abstract function __beforeTest();

	public abstract function __afterTest();
}

class Child extends ParentClass
{
	public function __beforeTest()
	{
		echo 'Child->__beforeTest()', PHP_EOL;
	}

	public function __afterTest()
	{
		echo 'Child->__afterTest()', PHP_EOL;
	}
}

$child = new Child;
$child->test();
```

运行结果：
```
Child->__beforeTest()
Parent->test()
Child->__afterTest()
```

这种写法你需要事先定义好前置和后置方法，如果需要前后置的方法一多，写起来会非常繁琐。

AOP 可以很好地解决这个问题，不仅可以在编写上不用事先定义这么多方法，还非常有助于解耦。

## AOP 名词

### 切面 Aspect

普通的类，你要切入的类。

### 切入点 Pointcut

普通类中的方法，你要切入的方法。

### 连接点 Joinpoint

在这个方法相关的什么时机触发通知，比如：调用的前置后置、抛出异常等。

### 通知 Advice

在连接点触发的通知，比如在前置操作触发，通知里写前置的具体实现。

IMI 支持的通知点有：

#### @Before

前置操作

#### @After

后置操作

#### @Around

环绕操作。先触发环绕操作，在前置操作前和后置操作后，都可以做一些事情。甚至可以完全不让原方法运行，在环绕中实现该方法功能。

#### @AfterReturning

在原方法返回后触发，可以修改返回值

#### @AfterThrowing

在抛出异常后触发，允许设置`allow`和`deny`，设置允许和拒绝捕获的异常类

## 使用方法

### 注解注入

监听池子的资源获取和释放：

```php
<?php
namespace Test;

use Imi\Aop\JoinPoint;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;

/**
 * @Aspect
 */
class Pool
{
	/**
	 * @PointCut(
	 * 		allow={
	 * 			"Imi\*Pool*::getResource",
	 * 			"Imi\*Pool*::release",
	 * 		}
	 * )
	 * @After
	 * @param JoinPoint $a
	 * @return void
	 */
	public function test(JoinPoint $joinPoint)
	{
		echo $joinPoint->getType() . ' ' . get_parent_class($joinPoint->getTarget()) . '::' . $joinPoint->getMethod() . '(): ' . $joinPoint->getTarget()->getFree() . '/' . $joinPoint->getTarget()->getCount() . PHP_EOL;
	}
}
```

运行效果：

```
after Imi\Redis\CoroutineRedisPool::getResource(): 0/1
after Imi\Redis\CoroutineRedisPool::release(): 1/1
```

类名、方法名和命名空间没有要求，只要`beanScan`里能扫描到即可。

类注释中必须写`@Aspect`表名是一个切面类

方法中写`@PointCut`表示指定切入点，支持通配符

`@After`代表在该方法调用后触发

### 配置注入

#### 实现代码

```php
namespace Test;

use Imi\Aop\JoinPoint;

class Test
{
	/**
	 * @param JoinPoint $a
	 * @return void
	 */
	public function test(JoinPoint $joinPoint)
	{
		echo $joinPoint->getType() . ' ' . get_parent_class($joinPoint->getTarget()) . '::' . $joinPoint->getMethod() . '(): ' . $joinPoint->getTarget()->getFree() . '/' . $joinPoint->getTarget()->getCount() . PHP_EOL;
	}
}
```

对类没有任何要求，方法只需要参数对即可。

#### 配置

```
<?php
return [
	// 类名
	\Test\Test::class	=>	[
		// 固定写法methods
		'methods'	=>	[
			// 方法名
			'test'	=>	[
				// 指定切入点
				'pointCut'	=>	[
					'allow'	=>	[
						"Imi\*Pool*::getResource",
						"Imi\*Pool*::release",
					]
				],
				'after'	=>	[
					
				]
			]
		]
	],
];
```