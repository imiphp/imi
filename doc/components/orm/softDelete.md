# 模型软删除

因为种种原因，现在大部分场景下，我们开发删除功能时，都不会将记录物理删除。

使用模型软删除功能非常简单，只需要在模型类中引入 trait `Imi\Model\SoftDelete\Traits\TSoftDelete`，并且在类上加上注解 `@SoftDelete`

## 示例

```php
<?php

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\SoftDelete\Annotation\SoftDelete;
use Imi\Model\SoftDelete\Traits\TSoftDelete;
use Imi\Test\Component\Model\Base\TestSoftDeleteBase;

/**
 * tb_test_soft_delete.
 *
 * @Inherit
 * @SoftDelete
 */
class TestSoftDelete extends TestSoftDeleteBase
{
    use TSoftDelete;
}
```

## 注解

### @SoftDelete

软删除

类名：`Imi\Model\SoftDelete\Annotation\SoftDelete`

| 属性名称 | 说明 |
| ------------ | ------------ 
| field | 软删除字段名，默认为`delete_time` |
| default | 软删除字段的默认值，代表非删除状态，默认为`0` |

## 使用

imi 模型软删除，默认逻辑是非删除状态字段值为 `0`，删除后值为当前时间戳。

你也可以自行定义字段名、默认值、删除后的值。

字段名、默认值可以通过 `@SoftDelete` 进行设置。

删除后的值可以通过覆盖模型类中的 `__generateSoftDeleteValue()` 方法实现：

```php
/**
 * 生成软删除字段的值
 *
 * @return mixed
 */
public function __generateSoftDeleteValue()
{
    return time();
}
```
