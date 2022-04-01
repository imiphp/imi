# 容器

容器用于存放类实例对象，容器中的对象类我们称之为 `Bean`。

通过容器实例化出来的对象，我们可以对它们进行注入操作。

所有通过注解实现的功能，对象如果是直接 `new` 出来的，是不会生效的，必须使用容器！

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

> imi v2.0 版本开始已经不一定需要配置 `beanScan` 了

## 实例化对象

实例化对象用法可以代替 `new`。

```php
// Bean 名称
\Imi\Bean\BeanFactory::newInstance('Bean名称');
// 具体的类名
\Imi\Bean\BeanFactory::newInstance(XXX::class);
// 传入构造方法的参数
\Imi\Bean\BeanFactory::newInstance(XXX::class, 1, 2);
```

## 容器分类

* 全局容器
* 服务器容器（继承全局容器）
* 请求上下文容器（继承服务器容器或全局容器）

### 全局容器

存活于框架整个生命周期，可以理解为全局容器。

```php
$object = \Imi\App::getBean('XXX');
```

### 服务器容器

只针对目标服务器的容器，该容器中目前存储了该服务器的路由对象及配置等等。

比如你的项目分别监听了 Http、TCP 两个协议端口，Http 请求接口进来，使用服务器容器获取到的对象，只会是这个 Http 服务独有的，TCP 服务里拿不到。

```php
$object = \Imi\Server\ServerManager::getServer('main')->getBean('XXX');
```

### 请求上下文容器

当前请求有效的容器，请求结束时即销毁。

```php
$object = \Imi\RequestContext::getBean('XXX');
```

### 全局单例容器

此方法实例化的对象，AOP、注解等都对它不产生作用，只是单纯的单例

```php
$object = \Imi\App::getSingleton('XXX');
```

## 容器绑定

**配置绑定：**

全局容器-项目配置文件：

```php
[
    'imi' => [
        'beans' => [
            'aaa' => XXX::class,
        ],
    ],
]
```

如上配置后，就可以使用 `\Imi\App::getBean('aaa')` 等方式实例化了

同理，你甚至可以使用自己写的类，配置覆盖 imi 内置的同名类

---

服务器容器：

> 详见各环境的服务器配置文档。

**动态绑定：**

全局容器：

```php
use Imi\App;

// 绑定
App::getContainer()->bind('aaa', XXX::class);

// 绑定带参数，非单例模式，禁用递归依赖
App::getContainer()->bind('aaa', XXX::class, \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_EACH_NEW, false);

// 实例化
$obj = App::getBean('aaa');
```

服务器容器：

```php
use Imi\Server\ServerManager;

// 绑定
ServerManager::getServer()->getContainer()->bind('aaa', XXX::class);

// 绑定带参数，非单例模式，禁用递归依赖
ServerManager::getServer()->getContainer()->bind('aaa', XXX::class, \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_EACH_NEW, false);

// 实例化
$obj = ServerManager::getServer()->getContainer()->getBean('aaa');
```

请求上下文容器：

```php
use Imi\RequestContext;

// 绑定
RequestContext::getContainer()->bind('aaa', XXX::class);

// 绑定带参数，非单例模式，禁用递归依赖
RequestContext::getContainer()->bind('aaa', XXX::class, \Imi\Bean\Annotation\Bean::INSTANCE_TYPE_EACH_NEW, false);

// 实例化
$obj = RequestContext::getBean('aaa');
```

> 禁用递归依赖可以规避服务启动后，第一次访问概率报错问题

## 容器对象类 (Bean)

`getBean()`时可以传带有完整命名空间的类名，或者别名，我们可以通过`@Bean`注解来定义别名。

你也可以在类里定义一个`__init()`方法，imi 将它作为第二个构造方法。

执行顺序：`__construct -> injectProps -> __init`

`injectProps` 即为属性注入，具体请看章节：[AOP](/components/aop/index.html)

定义：

```php
namespace Test;

/**
 * 下面两种写法相同（注意实际不要写多个 Bean 注解） 
 * @Bean("MyTest")
 * @Bean(name="MyTest")
 * 
 * 下面是禁用递归依赖和设置实例化类型，可以根据实际情况设置
 * @Bean(name="MyTest", instanceType=\Imi\Bean\Annotation\Bean::INSTANCE_TYPE_SINGLETON, recursion=false)
 * 
 * 下面是限制生效的环境，支持一个或多个
 * @Bean(name="MyTest", env="swoole")
 * @Bean(name="MyTest", env={"swoole", "workerman"})
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

## 注解继承

默认情况下，继承父类，父类的注解是不生效的。

有时候，我们需要让他生效。

imi 提供了一个 `@Inherit` 注解，支持写在：类、方法、属性、常量上。

类名：`Imi\Bean\Annotation\Inherit`

参数：

```php
/**
 * 允许的注解类，为 null 则不限制，支持字符串或数组
 *
 * @var string|string[]
 */
public $annotation;
```

例子：

下面是模型的例子，父类定义结构，子类中可以写自定义代码。重新生成模型父类代码，不会把自定义代码给覆盖掉。

父类：

```php
<?php
namespace Imi\Test\Component\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * ArticleBase
 * @Entity
 * @Table(name="tb_article", id={"id"})
 * @property int $id 
 * @property string $title 
 * @property string $content 
 * @property string $time 
 */
abstract class ArticleBase extends Model
{
    /**
     * id
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     * @var int
     */
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
     * @Column(name="title", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
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
     * @Column(name="content", type="mediumtext", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
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
     * @Column(name="time", type="timestamp", length=0, accuracy=0, nullable=false, default="CURRENT_TIMESTAMP", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
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

子类：

```php
<?php
namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Test\Component\Model\Base\ArticleBase;

/**
 * Article
 * @Inherit
 */
class Article extends ArticleBase
{

}
```
