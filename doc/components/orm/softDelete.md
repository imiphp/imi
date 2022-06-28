# 模型软删除

[toc]

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
| field | 软删除字段名，默认值见下方说明 |
| default | 软删除字段的默认值，代表非删除状态，默认为`0` |

`field` 不设置时，默认从配置 `@app.model.softDelete.fields.deleteTime` 读取，如果没有配置，默认是：`delete_time`(imi >= v2.0.16)

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

### 查询

如果模型引入了软删除机制，使用 `XXXModel::find()`、`XXXModel::query()` 等方式查询时，自动过滤被软删除的数据。

如果需要查询到被软删除的数据，请使用 `XXXModel::findDeleted()`。

如果想要查到所有数据（包括删除和未删除），可以使用 `XXXModel::originQuery()` 获取查询构建器。

### 删除

**软删除：**

```php
XXXModel::find(1)->delete();
```

**物理删除：**

```php
XXXModel::find(1)->hardDelete();
```

### 恢复记录

恢复被软删除的记录：

```php
XXXModel::findDeleted(1)-restore();
```
