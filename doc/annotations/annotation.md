# 编写自定义注解

[toc]

在 imi 框架中，使用注解可以实现很多功能。比如：路由、模型定义、事务、缓存等等

除了内置的注解以外，如果编写属于自己的注解呢？

这篇教程就来教大家来编写属于自己的注解。

## 注解定义

### 注解扫描

imi 是常驻内存运行的，所以冷启动时采用了全量扫描的方式，来实现注解缓存。使用的时候，就和读取配置一样简单高效。

### 注解类

每个注解都是一个类。

注解类需要继承`\Imi\Bean\Annotation\Base`类

```php
<?php
namespace ImiApp\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 示例注解
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * // 下面是IDE提示注释
 * @property string $name 随便定义的属性
 * @property int $age 随便定义的属性
 */
// 下面的是原生注解定义
#[\Attribute(\Attribute::TARGET_METHOD)]
class MyAnnotation extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 构造方法里定义注解的属性，并且设置默认值
     */
    public function __construct(?array $__data = null, string $name = '', int $age = 0)
    {
        parent::__construct(...\func_get_args());
    }
}
```

`defaultFieldName`定义的参数名，在你使用注解时候，如果只传这一个参数，可以省略参数名

写在类上的：

`@Annotation`注解，表示当前类是注解类。

`@Target`注解，表示当前注解可以写在什么上面。可选：`CLASS`、`METHOD`、`PROPERTY`、`CONST`。支持传多个的写法：`@Target({"CLASS", "METHOD", "PROPERTY", "CONST"})`

`@Parser`注解，指定扫描注解时候的处理器，可以不写该注解，或者填写`"\Imi\Bean\Parser\NullParser"`即可，详见下文[编写处理器](#编写处理器)

## 注解使用

由于注解是一个类，所以使用注解需要`use`它

```php
<?php
namespace ImiApp\Test;

use ImiApp\Annotation\MyAnnotation;

#[Bean(['Test'])]
class Test
{
    /**
     * @MyAnnotation("a")
     * @MyAnnotation(name="b", age=11)
     */
    // 下面是原生注解用法
    #[MyAnnotation(name: 'a')]
    #[MyAnnotation(name: 'b', age: 11)]
    public function aaa()
    {

    }

}
```

## 注入注解

imi 中可以注入带有注解的方法。

编写 AOP 类：

```php
namespace ImiApp\Aop;

use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\PointCutType;
use Imi\Aop\AroundJoinPoint;

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
     *             \ImiApp\Annotation\MyAnnotation::class
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function parseTransaction(AroundJoinPoint $joinPoint)
    {
        // 前置操作

        // 执行原方法，获取返回值
        $result = $joinPoint->proceed($args);
        // 执行原方法，获取返回值（方法返回值是引用返回时）
        // $result = $joinPoint->proceed($args, true);
        // 后置操作

        // 返回返回值，如有必要你也可以自己决定其他返回值
        return $result;
    }
}
```

具体用法请参考：<https://doc.imiphp.com/v2.1/components/aop/index.html>

## 获取注解

具体用法请参考：<https://doc.imiphp.com/v2.1/annotations/annotationManager.html>

## 编写处理器

一般如果没有特殊需求，可以不写处理器。

```php
<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

class MyParser extends \Imi\Bean\Parser\BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        var_dump($annotation); // 注解对象
        var_dump($className); // 注解所在类名
        var_dump($target); // 注解所在目标，self::TARGET_XXX 常量
        var_dump($targetName); // 注解所在目标名称。例：如果在方法上，就是方法名

        // YourManager::set(); // 可选：你可以将处理后的数据，写入一个你自定义的静态类中，这个类没有硬性规定，随便你怎么写
    }
}
```

如果你希望把在处理器中存储的数据存到 `runtime` 缓存，可以在项目的 `Main` 类中监听事件来实现。

```php
<?php

declare(strict_types=1);

namespace ImiApp;

use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Log\Log;

class Main extends \Imi\Main\AppBaseMain
{
    public function __init(): void
    {
        // 监听读取 Runtime 缓存
        Event::on('IMI.LOAD_RUNTIME_INFO', function (EventParam $e) {
            $cacheName = $e->getData()['cacheName']; // 缓存名称
            $data = $e->getData()['data']; // 数组
            YourManager::setData($data['myData'] ?? []); // 从 runtime 缓存读取到管理类里，myData 是自定义键名，可以换为别的
        });
        // 监听构建 Runtime 缓存
        Event::on('IMI.BUILD_RUNTIME', function (EventParam $e) {
            $cacheName = $e->getData()['cacheName']; // 缓存名称
            $data = $e->getData()['data']; // 数组
            $data['myData'] = YourManager::getData(); // 写入 runtime 缓存，myData 是自定义键名，可以换为别的
        });
    }
}
```
