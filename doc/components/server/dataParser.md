# 内置的数据处理器

## JsonArrayParser

JSON 数组

类名：`\Imi\Server\DataParser\JsonArrayParser::class`

支持注入的属性：

名称 | 描述 | 默认值
-|-|-
options | JSON 序列化时的参数 | 0 |
depth | 设置最大深度。 必须大于0。 | 512 |

## JsonObjectParser

JSON 对象

类名：`\Imi\Server\DataParser\JsonObjectParser::class`

支持注入的属性：

名称 | 描述 | 默认值
-|-|-
options | JSON 序列化时的参数 | 0 |
depth | 设置最大深度。 必须大于0。 | 512 |

## 自定义数据处理器

实现接口：`Imi\Server\DataParser\IParser`

```php
<?php

namespace App\DataParser;

use Imi\Server\DataParser\IParser;

class XXXParser implements IParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function encode($data)
    {
    }

    /**
     * 解码为php变量.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function decode($data)
    {
    }
}
```
