# AOP

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

### 使用注解注入方法

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

### 注入带有注解的方法

可参考`imi\src\Db\Aop\TransactionAop.php`文件：

```php
/**
 * @Aspect
 */
class TransactionAop
{
    /**
     * 自动事务支持
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Transaction::class
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function parseTransaction(AroundJoinPoint $joinPoint)
    {
	}
}
```

> 无论这个注解在方法上出现了几次，都只会触发一次注入处理

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

```php
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

## 所有注入演示

```php
<?php
namespace Test;

use Imi\Aop\JoinPoint;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\AfterReturning;

/**
 * @Aspect
 */
class Test
{
	/**
	 * 前置操作
	 * @PointCut(
	 *         allow={
	 *             "ImiDemo\HttpDemo\MainServer\Model\Goods::getScore",
	 *         }
	 * )
	 * @Before
	 * @param JoinPoint $a
	 * @return void
	 */
	public function before(JoinPoint $joinPoint)
	{
		echo 'getScore()-before', PHP_EOL;
	}

	/**
	 * 后置操作
	 * @PointCut(
	 *         allow={
	 *             "ImiDemo\HttpDemo\MainServer\Model\Goods::getScore",
	 *         }
	 * )
	 * @After
	 * @param JoinPoint $a
	 * @return void
	 */
	public function after(JoinPoint $joinPoint)
	{
		echo 'getScore()-after', PHP_EOL;
	}

	/**
	 * 环绕
	 * @PointCut(
	 * 		allow={
	 * 			"ImiDemo\HttpDemo\MainServer\Model\Goods::getScore1",
	 * 		}
	 * )
	 * @Around
	 * @return mixed
	 */
	public function around(AroundJoinPoint $joinPoint)
	{
		var_dump('调用前');
		// 调用原方法，获取返回值
		$result = $joinPoint->proceed();
		var_dump('调用后');
		return 'value'; // 无视原方法调用后的返回值，强制返回一个其它值
		return $result; // 返回原方法返回值
	}

	/**
	 * 返回值
	 * @PointCut(
	 * 		allow={
	 * 			"ImiDemo\HttpDemo\MainServer\Model\Goods::getScore",
	 * 		}
	 * )
	 * @AfterReturning
	 * @param AfterReturningJoinPoint $joinPoint
	 * @return void
	 */
	public function afterReturning(AfterReturningJoinPoint $joinPoint)
	{
		$joinPoint->setReturnValue('修改返回值');
	}

	/**
	 * 异常捕获
	 * @PointCut(
	 * 		allow={
	 * 			"ImiDemo\HttpDemo\MainServer\Model\Goods::getScore",
	 * 		}
	 * )
	 * @AfterThrowing
	 * @param AfterThrowingJoinPoint $joinPoint
	 * @return void
	 */
	public function afterThrowing(AfterThrowingJoinPoint $joinPoint)
	{
		// 异常不会被继续抛出
		$joinPoint->cancelThrow();
		var_dump('异常捕获:' . $joinPoint->getThrowable()->getMessage());
	}
}

```

### 属性注入

如下代码例子，定义一个类，使用`@Inject`注解来注释属性，在通过`getBean()`实例化时，会自动给被注释的属性赋值相应的实例对象。

```php
namespace Test;

class TestClass
{
	/**
	 * 某Model对象
	 * @Inject("XXX\Model\User")
	 */
	protected $model;
	
	public function test()
	{
		var_dump($model->toArray());
	}
}

$testClass = App::getBean('Test\TestClass');
$testClass->test();
```

### 方法参数注入

```php
/**
 * @InjectArg(name="a", value="123")
 * @InjectArg(name="b", value=@Inject("\ImiDemo\HttpDemo\MainServer\Model\User"))
 *
 * @return void
 */
public function test($a, $b)
{
	var_dump($a, $b);
}
```

可以直接注入值，也可以使用值注入注解。
