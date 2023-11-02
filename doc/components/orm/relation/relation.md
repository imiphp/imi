# 自定义关联

在 imi 中除了一对一等传统关联以外，你还可以自定义关联处理。

## 使用

### 注解

在属性上声明注解 `Relation` (`Imi\Model\Annotation\Relation\Relation`) 使用自定义关联。

### 声明关联的属性

```php
#[
    Relation,
    AutoSave,
    AutoDelete
]
public ?array $relation = null;

public function getRelation(): ?array
{
    return $this->relation;
}

public function setRelation(?array $relation): self
{
    $this->relation = $relation;

    return $this;
}
```

* `Relation` 是必选项

* `AutoSave` 可启用 `insert` 和 `update` 处理

* `AutoInsert` 可单独启用 `insert` 处理

* `AutoUpdate` 可单独启用 `update` 处理

* `AutoDelete` 可启用 `delete` 处理

* `AutoSelect` 可启用 `query` 处理，默认开启无需设置

### 实现自定义关联

#### 插入

在类中定义方法：

```php
// 一定要声明为 public static
// 方法名规则：__insert + 首字母大写的属性名
public static function __insertRelation(self $model, \Imi\Model\Annotation\Relation\Relation $annotation): void
{
    // 你可以直接操作 $model 对象，跟操作 $this 一样
    $model->xxx = 123;
}
```

#### 更新

在类中定义方法：

```php
// 一定要声明为 public static
// 方法名规则：__update + 首字母大写的属性名
public static function __updateRelation(self $model, \Imi\Model\Annotation\Relation\Relation $annotation): void
{
    // 你可以直接操作 $model 对象，跟操作 $this 一样
    $model->xxx = 123;
}
```

#### 删除

在类中定义方法：

```php
// 一定要声明为 public static
// 方法名规则：__delete + 首字母大写的属性名
public static function __deleteRelation(self $model, \Imi\Model\Annotation\Relation\Relation $annotation): void
{
    // 你可以直接操作 $model 对象，跟操作 $this 一样
    $model->xxx = 123;
}
```

#### 查询

在类中定义方法：

```php
// 一定要声明为 public static
// 方法名规则：__query + 首字母大写的属性名
// 需要注意：第一个参数是模型对象属性，与上面其它操作不同
/**
 * @param self[] $models
 */
public static function __queryRelation(array $models, \Imi\Model\Annotation\Relation\Relation $annotation): void
{
    foreach ($models as $model)
    {
        // 你可以直接操作 $model 对象，跟操作 $this 一样
        $model->xxx = 123;
    }
}
```
