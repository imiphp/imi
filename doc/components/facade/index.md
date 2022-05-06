# 门面 (Facade)

[toc]

imi 现已支持门面 (Facade) 特性，支持为Bean对象提供一个静态调用方法的类。

## 示例

假设有这么一个类：

```php
<?php
namespace Imi\Test\Component\Facade;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("FacadeA")
 */
class A
{
    public function add($a, $b)
    {
        return $a + $b;
    }

}
```

定义 Facade 类：

```php
<?php
namespace Imi\Test\Component\Facade;

use Imi\Facade\BaseFacade;
use Imi\Facade\Annotation\Facade;

/**
 * @Facade("FacadeA")
 * @method mixed add($a, $b)
 */
class FacadeA extends BaseFacade
{

}
```

> 继承 `Imi\Facade\BaseFacade` 类，然后使用`@Facade`注解类定义绑定到哪个类。

使用：

```php
use Imi\Test\Component\Facade\FacadeA;
FacadeA::add(1, 2); // 3
```

## @Facade 注解

```php
class Facade extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string|null
     */
    protected ?string $defaultFieldName = 'class';

    /**
     * 类名，支持 Bean 名
     *
     * @var string
     */
    public $class;

    /**
     * 为 true 时，使用当前请求上下文的 Bean 对象
     *
     * @var boolean
     */
    public $request = false;

    /**
     * 实例化参数
     *
     * @var array
     */
    public $args = [];

}
```

## Facade 生成器

查看帮助：

`imi-xxx generate/facade -h`
