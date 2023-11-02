# 容器

[toc]

imi 框架的容器采用了依赖注入 (Dependency Injection，简称 DI) 的设计模式，用于管理和注入对象及其依赖关系。

imi 框架的容器包括全局容器、服务器容器和请求上下文容器。

在 imi 框架中，通过注解实现的功能，对象如果是直接 `new` 出来的，是不会生效的，必须使用容器！因为 imi 框架的容器可以管理对象及其依赖关系，并实现依赖注入。

使用容器可以更方便地管理对象的生命周期和依赖关系，提高应用程序的可维护性和性能表现。

## 实例化对象

实例化对象用法可以代替 `new`。

```php
// 具体的类名
\Imi\Bean\BeanFactory::newInstance(XXX::class);
// 传入构造方法的参数
\Imi\Bean\BeanFactory::newInstance(XXX::class, 1, 2);
```

## 容器分类

* 全局容器
* 服务器容器（继承全局容器）
* 请求上下文容器（继承服务器容器或全局容器）
* 全局单例容器（不支持 AOP、注解等）

### 全局容器

全局容器是全局共享的，用于存储应用程序中的单例对象。

全局容器存活于框架整个生命周期，是一种全局的、共享的容器，可以被整个应用程序的代码访问。

**获取对象：**

```php
$object = \Imi\App::getBean('XXX');
$object = \Imi\App::getBean('XXX', 1, 2, 3); // 支持实例化参数

// 获取容器对象
$container = \Imi\App::getContainer();
$object = $container->get('XXX');
```

**配置类别名：**

方法一-注解：

```php
<?php
use Imi\Bean\Annotation\Bean;

#[Bean(name: 'XXX')]
class Test
{

}
```

方法二-项目配置文件：

```php
[
    'imi' => [
        'beans' => [
            'XXX' => Test::class,
        ],
    ],
]
```

如上配置后，就可以使用 `\Imi\App::getBean('XXX')` 等方式实例化了

同理，你甚至可以使用自己写的类，配置覆盖 imi 内置的同名类

**动态绑定：**

```php
use Imi\App;

// 绑定
App::getContainer()->bind('aaa', XXX::class);

// 绑定带参数，非单例模式，禁用递归依赖
App::getContainer()->bind('aaa', XXX::class, \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_EACH_NEW, false);

// 实例化，带缓存
$obj = App::getBean('aaa');

// 实例化，带参数，不缓存
$obj = App::getBean('aaa', 1);

// 绑定回调
App::getContainer()->bindCallable('bbb', function(string $id, int $a) {
    var_dump($id); // aaa
    var_dump($a);  // 123
    // 返回你要实例化的对象，这里只是示例
    return new \stdClass;
});
$obj = App::getBean('aaa', 123);

// 设置实例
App::getContainer()->set('ccc', new \stdClass);
App::getContainer()->getBean('ccc');
```

> 禁用递归依赖可以规避服务启动后，第一次访问概率报错问题

### 服务器容器

服务器容器是针对每个子服务器独立的，用于存储服务器级别的对象。

服务器容器中存储的对象是针对目标服务器的，不同服务器之间的容器是互相独立的。

在服务器容器中，目前存储了该服务器的路由对象及配置等等。

比如，如果你的项目同时监听了 Http、TCP 两个协议端口，那么在 Http 请求接口进来时，通过服务器容器获取到的对象只会是该 Http 服务独有的，在 TCP 服务中是无法访问的。

**获取对象：**

```php
$object = \Imi\Server\ServerManager::getServer('main')->getBean('XXX');

// 获取容器对象
$container = \Imi\Server\ServerManager::getServer('main')->getContainer();
$object = $container->get('XXX');
```

**配置类别名：**

方法一-注解：

```php
<?php
use Imi\Bean\Annotation\Bean;

#[Bean(name: 'XXX')]
class Test
{

}
```

方法二-项目配置文件：

> 默认情况下继承全局容器配置，服务器容器配置因环境不同而有所差异，具体细节可以参考各环境的服务器配置文档。

**动态绑定：**

```php
use Imi\Server\ServerManager;

// 绑定
ServerManager::getServer()->getContainer()->bind('aaa', XXX::class);

// 绑定带参数，非单例模式，禁用递归依赖
ServerManager::getServer()->getContainer()->bind('aaa', XXX::class, \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_EACH_NEW, false);

// 实例化，带缓存
$obj = ServerManager::getServer()->getContainer()->getBean('aaa');

// 实例化，带参数，不缓存
$obj = ServerManager::getServer()->getContainer()->getBean('aaa', 1);
```

> 禁用递归依赖可以规避服务启动后，第一次访问概率报错问题

### 请求上下文容器

请求上下文容器是当前请求有效的容器，请求结束时即销毁。

请求上下文容器中存储的对象是针对当前请求有效的，请求结束后会自动销毁。

有关更多细节可以参考[请求上下文](/v3.0/core/requestContext.html)。

请求上下文容器可以为每个请求提供独立的容器实例，以存储请求处理中所需的对象，例如请求参数、数据库连接等。请求上下文容器的使用可以避免多个请求之间的对象冲突，提高应用程序的可靠性和稳定性。

**获取对象：**

```php
$object = \Imi\RequestContext::getBean('XXX');

// 获取容器对象
$container = \Imi\RequestContext::getContainer();
$object = $container->get('XXX');
```

**配置类别名：**

方法一-注解：

```php
<?php
use Imi\Bean\Annotation\Bean;

#[Bean(name: 'XXX')]
class Test
{

}
```

方法二-项目配置文件：

> 继承全局容器或服务器容器配置

**动态绑定：**

请求上下文容器：

```php
use Imi\RequestContext;

// 绑定
RequestContext::getContainer()->bind('aaa', XXX::class);

// 绑定带参数，非单例模式，禁用递归依赖
RequestContext::getContainer()->bind('aaa', XXX::class, \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_EACH_NEW, false);

// 实例化，带缓存
$obj = RequestContext::getBean('aaa');

// 实例化，带参数，不缓存
$obj = RequestContext::getBean('aaa', 1);
```

> 禁用递归依赖可以规避服务启动后，第一次访问概率报错问题

### 全局单例容器

全局单例容器可以用于实例化全局共享的单例对象，该对象不会被 AOP、注解等功能所影响，只是单纯的单例。

可以通过全局单例容器在整个应用程序中共享同一个对象实例，提高应用程序的性能和效率。

**获取对象：**

```php
$object = \Imi\App::getSingleton('XXX');
```

**配置类别名：**

> 同全局容器

**动态绑定：**

> 同全局容器

### 全局容器实例化

全局容器实例化方法可以用于每次调用时实例化并返回新的对象，同时 AOP、注解等功能仍然有效。

通过全局容器实例化方法可以轻松地获取到全局范围内的新对象，可以为每个调用提供独立的对象实例，避免对象冲突和资源浪费。

**实例化对象：**

```php
$object = \Imi\App::newInstance('XXX');
$object = \Imi\App::newInstance('XXX', 1, 2, 3); // 支持实例化参数
```

**配置类别名：**

> 同全局容器

**动态绑定：**

> 同全局容器

## 容器对象类 (Bean)

在容器中，可以通过 `getBean()` 方法来获取已经注册的 Bean 对象。在调用 `getBean()` 方法时，可以传递带有完整命名空间的类名或者别名。如果需要定义别名，可以使用 `Bean` 注解进行定义。

除此之外，在 Bean 类中还可以定义一个名为 `__init()` 的方法，该方法会作为第二个构造方法被执行。具体的执行顺序为：`__construct() -> injectProps() -> __init()`。其中，`injectProps()` 方法用于属性注入，具体内容可以参考 [AOP 章节](/components/aop/index.html)的相关内容。

**定义：**

```php
namespace Test;

// 智能
#[Bean()]
// 指定名称
#[Bean(name: 'MyTest')]
// 下面是禁用递归依赖和设置实例化类型，可以根据实际情况设置
#[Bean(instanceType: \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_SINGLETON, recursion: false)]
// 下面是限制生效的环境，支持一个或多个
#[Bean(env: 'swoole')]
#[Bean(env: ['swoole', 'workerman'])]
class ABCDEFG
{
    public function __construct($id)
    {
        echo 'first', PHP_EOL;
    }

    public function __init($id): void
    {
        echo 'second first', PHP_EOL;
    }
}
```

**获得实例：**

```php
App::getBean('MyTest');
App::getBean(\Test\ABCDEFG::class);
```

## 注解继承

默认情况下，子类继承父类时，父类的注解是不会生效的。但是有时候我们需要让父类的注解生效。在这种情况下，可以在类、方法、属性或常量上使用 `Inherit` 注解来实现。

**类名：**`Imi\Bean\Annotation\Inherit`

**参数：**

```php
/**
 * 允许的注解类，为 null 则不限制，支持字符串或数组
 *
 * @var string|string[]
 */
public $annotation;
```

**例子：**

下面是一个模型的例子，我们可以在父类中定义一些结构，然后在子类中编写自定义代码。如果重新生成模型的父类代码，这些自定义代码不会被覆盖掉。

**父类：**

```php
<?php
namespace Imi\Test\Component\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * ArticleBase
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $time
 */
#[
    Entity,
    Table(name: 'tb_article', id=["id"])
]
abstract class ArticleBase extends Model
{
    /**
     * id
     * @var int
     */
    #[Column(name: 'id', type: 'int', length=10, accuracy=0, nullable=false, default='', isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)]
    protected $id;

    /**
     * 获取 id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id
     * @param int $id id
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * title
     * @var string
     */
    #[Column(name: 'title', type: 'varchar', length=255, accuracy=0, nullable=false, default='', isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)]
    protected $title;

    /**
     * 获取 title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * 赋值 title
     * @param string $title title
     * @return static
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * content
     * @var string
     */
    #[Column(name: 'content', type: 'mediumtext', length=0, accuracy=0, nullable=false, default='', isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)]
    protected $content;

    /**
     * 获取 content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 赋值 content
     * @param string $content content
     * @return static
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * time
     * @var string
     */
    #[Column(name: 'time', type: 'timestamp', length=0, accuracy=0, nullable=false, default='CURRENT_TIMESTAMP', isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)]
    protected $time;

    /**
     * 获取 time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * 赋值 time
     * @param string $time time
     * @return static
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

}
```

**子类：**

```php
<?php
namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Test\Component\Model\Base\ArticleBase;

/**
 * Article
 */
#[Inherit]
class Article extends ArticleBase
{

}
```

## 配置扫描命名空间

应用启动时，会扫描配置文件中指定的命名空间，所有被扫描到的类才可以通过容器来获取。

在项目和子服务器的配置文件中，可以通过配置来指定需要扫描的命名空间。

```php
return [
    'beanScan'  =>  [
        'ImiApp\Model',
        'ImiApp\Service',
    ],
];
```

> imi v2.0 版本开始已经不一定需要配置 `beanScan` 了
