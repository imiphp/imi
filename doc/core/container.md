# 容器

容器用于存放类实例对象，容器中的对象类我们称之为 `Bean`。通过容器实例化出来的对象，我们可以对它们进行注入操作。

## 配置扫描命名空间

应用启动时，会扫描配置中设定的命名空间，扫描到的类才可以通过容器使用 `Bean` 名称获取对象。

支持在项目、子服务器的配置文件中配置：

```php
return [
    'beanScan'  =>  [
        'ImiApp\Model',
        'ImiApp\Service',
    ],
];
```

## 容器分类

* 全局容器
* 服务器容器
* 请求上下文容器

### 全局容器

存活于框架整个生命周期，可以理解为全局容器。

```php
$object = \Imi\App::getBean('XXX');
```

### 服务器容器

只针对目标服务器的容器，该容器中目前存储了该服务器的路由对象及配置等等。

```php
$object = \Imi\ServerManage::getServer('main')->getBean('XXX');
```

### 请求上下文容器

当前请求有效的容器，请求结束时即销毁。

```php
$object = \Imi\RequestContext::getBean('XXX');
```

## 容器对象类 (Bean)

`getBean()`时可以传带有完整命名空间的类名，或者别名，我们可以通过`@Bean`注解来定义别名。

你也可以在类里定义一个`__init()`方法，imi 将它作为第二个构造方法。

执行顺序：`__construct -> injectProps -> __init`

`injectProps` 即为属性注入，具体请看章节：[AOP](/components/aop/index.html)

定义：

```php
namespace Test;

/**
 * @Bean("MyTest")
 */
class ABCDEFG
{
    public function __construct($id)
    {
        echo 'first', PHP_EOL;
    }

    public function __init($id)
    {
        echo 'second first', PHP_EOL;
    }
}
```

获得实例：

```php
App::getBean('MyTest');
App::getBean(\Test\ABCDEFG::class);
```
