# Partial

[toc]

imi 的 `Partial` 特性是从 C# 中借鉴而来，可以将一个类的部分分成几个文件来书写，使用trait编写，并在注解中加上 `@Partial`，可以方便地注入。

在实际开发中，`Partial` 特性有以下应用场景：

* 多人协作开发，可以将不同的方法分配给不同的人开发，每个人只需要在自己的文件中编写，不用担心冲突问题。
* 类的方法特别多，并且无法很好地拆分，此时可以将类分成几个文件来维护。
* 有一些类是通过工具生成的，如果我们在类里编写了自己的代码，又需要重新生成一些内容，这时候会覆盖我们编写的代码。使用 `Partial` 在另一个文件中开发，不会被覆盖掉。

## 使用方法

**需要被注入的类：**

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

**定义 Partial：**

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

**调用：**

```php
/** @var \Imi\Test\Component\Partial\Classes\PartialClassA $test */
$test = App::getBean('PartialClassA');
var_dump($test->test1());
// 原类中没定义，但通过 partial 可以正常调用
// 通过反射也可以获取到，是实实在在存在的方法和属性
var_dump($test->test2());
var_dump($test->test2Value);
```

**注意事项：**

* 命名空间 `namespace` 必须用 `{}` 括起来。

* 下面一部分代码是为方便 IDE 提示而写，非必须。如果写，则必须在里面写 `if(false){}`，否则会出现重复定义类错误！

* 被注入的类、`Partial` 定义类。

* 使用被注入类对象时，必须通过容器，否则不生效。
