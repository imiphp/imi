# 自己动手开发命令行工具

imi 的命令行工具使用注解来定义

## 注解

### @Tool

`Imi\Tool\Annotation\Tool`，写在类上，用于定义工具名称

| 属性名称 | 说明 |
|-|-
| name | 工具名称 |

### @Operation

`Imi\Tool\Annotation\Operation`，写在方法上，用于定义工具动作名称

| 属性名称 | 说明 |
|-|-
| name | 工具动作名称 |
| co | 是否自动开启协程，默认为`true` |

### @Arg

`Imi\Tool\Annotation\Arg`，写在方法上，可以有多个，用于定义工具参数

| 属性名称 | 说明 |
|-|-
| name | 参数名称 |
| type | 参数类型，支持：`string/int/float/boolean/array`，也可以使用`\Imi\Tool\ArgType::XXX` |
| default | 默认值 |
| required | 是否是必选参数，默认`false` |
| comments | 注释 |

## 工具定义

```php
namespace ImiApp\Tool;

use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Arg;

/**
 * @Tool("test")
 */
class Test
{
    /**
     * @Operation("hello")
     * @Arg(name="username", type="string", default="默认值")
     */
    public function hello($username)
    {
        echo "hello {$username}", PHP_EOL;
    }

}
```

## beanScan

工具所在命名空间必须在项目配置`@app.beanScan`中定义

如上面例子中的`ImiApp\Tool`必须定义在`@app.beanScan`

## 加载注解

默认命令行工具是不加载项目子模块（子服务器）中的注解的，如需使用请调用：

```php
\Imi\Bean\Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
```

## 工具调用

`imi 工具名称/动作名称 -参数名 参数值`

上面的例子调用示例：

`imi test/hello -username yurun`
