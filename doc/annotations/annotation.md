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

`@Parser`注解，指定扫描注解时候的处理器，可以不写该注解，或者填写`"\Imi\Bean\Parser\NullParser"`即可

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
        // 后置操作

        // 返回返回值，如有必要你也可以自己决定其他返回值
        return $result;
    }
}
```

具体用法请参考：<https://doc.imiphp.com/v2.1/components/aop/index.html>

## 获取注解

具体用法请参考：<https://doc.imiphp.com/v2.1/annotations/annotationManager.html>
