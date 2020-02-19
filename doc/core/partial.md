# Partial

imi 的 `Partial` 思想是从 C# 中借鉴而来。`Partial` 可以将一个类的部分，分成几个文件，分别书写。

截止目前最新的 PHP 7.4 版本，还未在语言层面上支持 `Partial` 特性。

但依赖于 imi 强大的容器对象，终于在 PHP 中实现了 `Partial` 特性。

使用非常简单，使用 `trait` 编写，加上 `@Partial` 注解，可以方便地注入。

## Partial 使用场景

* 多人协作开发，修改同一个类文件难免有冲突。如果事先定义好接口，将不同方法分配给几个人开发，每个人只需要在自己的文件中编写，不用担心冲突问题。

* 类的方法特别多，并且无法很好地拆分。这时候分到几个文件中，会相对比较好维护一些。

* 有一些类是通过工具生成的，如果我们在类里编写了自己的代码，又需要重新工具生成一些内容，这时候会覆盖我们编写的代码。使用 `Partial` 在另一个文件开发，不会被覆盖掉。

> 在 `Partial` 概念引入 imi 框架前，imi 的模型生成工具将生成代码放在父类，用户在子类中编写代码。

## 使用方法

需要被注入的类：

```php
<?php
namespace Imi\Test\Component\Partial\Classes;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("PartialClassA")
 */
class PartialClassA
{
    public function test1()
    {
        return 1;
    }

}

```

定义 Partial：

```php
<?php
namespace Imi\Test\Component\Partial\Partial
{
    use Imi\Bean\Annotation\Partial;

    /**
     * @Partial(Imi\Test\Component\Partial\Classes\PartialClassA::class)
     */
    trait TPartialClassA1
    {
        public $test2Value = 2;

        public function test2()
        {
            return $this->test2Value;
        }

    }

}

// 以下为方便 IDE 提示而写，非必须
namespace Imi\Test\Component\Partial\Classes
{
    // 必须用这个括起来，代码不会执行，但是 IDE 提示有效
    if(false)
    {
        class PartialClassA
        {
            public $test2Value;

            public function test2()
            {

            }
        }
    }

}
```

调用：

```php
/** @var \Imi\Test\Component\Partial\Classes\PartialClassA $test */
$test = App::getBean('PartialClassA');
var_dump($test->test1());
// 原类中没定义，但通过 partial 可以正常调用
// 通过反射也可以获取到，是实实在在存在的方法和属性
var_dump($test->test2());
var_dump($test->test2Value);
```

注意事项：

* 命名空间 `namespace` 必须用 `{}` 括起来。

* 下面一部分代码是为方便 IDE 提示而写，非必须。如果写，则必须在里面写 `if(false){}`，否则会出现重复定义类错误！

* 被注入的类、`Partial` 定义类，必须能被配置文件中配置的 `beanScan` 扫描到。

* 使用被注入类对象时，必须通过容器，否则不生效。
