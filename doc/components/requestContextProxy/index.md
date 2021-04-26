# 请求上下文代理（RequestContextProxy）

可以代理请求上下文中的数据，支持静态方法调用和对象方法调用。

## 示例

假设有这么一个类：

```php
<?php

namespace Imi\Test\Component\RequestContextProxy;

class A
{
    public function add($a, $b)
    {
        return $a + $b;
    }
}

```

定义代理类 RequestContextProxy：

```php
<?php
namespace Imi\Test\Component\RequestContextProxy;

use Imi\RequestContextProxy\BaseRequestContextProxy;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;

/**
 * @RequestContextProxy(class="Imi\Test\Component\RequestContextProxy\A", name="testRequestContextProxyA")
 * @method mixed add($a, $b)
 * @method static mixed add($a, $b)
 */
class RequestContextProxyA extends BaseRequestContextProxy
{

}

```

> 继承 `Imi\RequestContextProxy\BaseRequestContextProxy` 类，然后使用`@RequestContextProxy`注解类定义绑定到上下文的名称。

使用方式：

```php
use Imi\Test\Component\RequestContextProxy\RequestContextProxyA;
RequestContextProxyA::add(1, 2); // 3

// 也可以实例化后使用
$a = new RequestContextProxyA();
$a->add(2, 3); // 5

// 获取请求上下文中的实例
$a = RequestContextProxyA::__getProxyInstance();
$a->add(2, 3); // 5

// 设置请求上下文中的实例
RequestContextProxyA::__setProxyInstance($a);
```

## @RequestContextProxy 注解

```php
class RequestContextProxy extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 代理类名.
     *
     * @var string
     */
    public $class;

    /**
     * 请求上下文中的名称.
     *
     * @var string
     */
    public $name;
}
```

## RequestContextProxy 生成器

查看帮助：

`imi generate/requestContextProxy -h`
