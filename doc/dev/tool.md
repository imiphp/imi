# 自己动手开发命令行工具

imi 的命令行工具使用注解来定义

## 注解

### @Command

`Imi\Cli\Annotation\Command`，写在类上，用于定义工具名称

| 属性名称 | 说明 |
|-|-
| name | 工具名称 |
| description | 操作描述 |

### @CommandAction

`Imi\Cli\Annotation\CommandAction`，写在方法上，用于定义工具动作名称

| 属性名称 | 说明 |
|-|-
| name | 工具动作名称 |
| co | 是否自动开启协程，默认为`true` |

### @Argument

`Imi\Cli\Annotation\Argument`，写在方法上，可以有多个，用于定义工具参数

| 属性名称 | 说明 |
|-|-
| name | 参数名称 |
| type | 参数类型，支持：`string/int/float/boolean/array`，也可以使用`\Imi\Cli\ArgType::XXX` |
| default | 默认值 |
| required | 是否是必选参数，默认`false` |
| comments | 注释 |
| to | 将参数值绑定到指定名称的参数 |

### @Option

`Imi\Cli\Annotation\Option`，写在方法上，可以有多个，用于可选项参数

| 属性名称 | 说明 |
|-|-
| name | 参数名称 |
| shortcut | 参数短名称 |
| type | 参数类型，支持：`string/int/float/boolean/array`，也可以使用`\Imi\Cli\ArgType::XXX` |
| default | 默认值 |
| required | 是否是必选参数，默认`false` |
| comments | 注释 |
| to | 将参数值绑定到指定名称的参数 |

## 工具定义

```php
namespace ImiApp\Tool;

use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Contract\BaseCommand;

/**
 * @Command("test")
 */
class Test extends BaseCommand
{
    /**
     * @CommandAction(name="hello", description="Hello world")
     * @Argument(name="content", type=\Imi\Cli\ArgType::STRING)
     * @Option(name="username", type=\Imi\Cli\ArgType::STRING, default="默认值")
     */
    public function hello(string $content, string $username): void
    {
        echo "{$username}: {$content}", PHP_EOL;

        // 通过 input 对象获取参数
        $this->input->getArgument('content');
        $this->input->getOption('username');

        // 通过 output 对象输出
        $this->output->writeln("{$username}: {$content}");
    }

}
```

## 工具调用

`imi 工具名称/动作名称 -参数名 参数值`

上面的例子调用示例：

`imi test/hello "content内容" --username yurun`

## 全局任意地方获取 input、output 对象

```php
use Imi\Cli\ImiCommand;
$input = ImiCommand::getInput();
$output = ImiCommand::getOutput();
```
