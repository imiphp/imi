# 介绍

通过预先确定好模型之间的关系，在业务开发中，使用非常简便的写法，就可以实现复杂的涉及多表数据增删改查。

这一切都是模型底层实现帮你在处理，在 imi 中，模型的关联关系都使用注解来定义。

## 注解

这里列出定义模型关联关系所需的注解，所有关联模型的注解，命名空间为`Imi\Model\Annotation\Relation`

### @OneToOne

一对一关系声明

**用法：**

`@OneToOne("模型类名")`

`@OneToOne(model="模型类名")`

查询时指定字段：

`@OneToOne(model="模型类名", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@OneToOne(model="模型类名", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@OneToOne(model="模型类名", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @OneToMany

一对多关系声明

**用法：**

`@OneToMany("模型类名")`

`@OneToMany(model="模型类名", order="排序规则字符串，例：age desc, id desc", limit=null)`

查询时指定字段：

`@OneToMany(model="模型类名", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@OneToMany(model="模型类名", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@OneToMany(model="模型类名", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @ManyToMany

多对多关系声明

**用法：**

`@ManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", order="排序规则字符串，例：age desc, id desc", limit=null)`

查询时指定字段：

`@ManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@ManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@ManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @PolymorphicOneToOne

多态一对一关系声明

**用法：**

`@PolymorphicOneToOne(model="关联模型", type="多态类型字段名", typeValue="多态类型值")`

查询时指定字段：

`@PolymorphicOneToOne(model="关联模型", type="多态类型字段名", typeValue="多态类型值", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@PolymorphicOneToOne(model="关联模型", type="多态类型字段名", typeValue="多态类型值", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@PolymorphicOneToOne(model="关联模型", type="多态类型字段名", typeValue="多态类型值", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @PolymorphicOneToMany

多态一对多关系声明

**用法：**

`@PolymorphicOneToMany(model="关联模型", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", limit=null)`

查询时指定字段：

`@PolymorphicOneToMany(model="关联模型", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@PolymorphicOneToMany(model="关联模型", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@PolymorphicOneToMany(model="关联模型", type="多态类型字段名", typeValue="多态类型值", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @PolymorphicManyToMany

多态多对多关系声明

**用法：**

`@PolymorphicManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", limit=null)`

查询时指定字段：

`@PolymorphicManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@PolymorphicManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@PolymorphicManyToMany(model="关联模型", middle="中间模型类名", rightMany="属性名，赋值为关联的模型对象列表", type="多态类型字段名", typeValue="多态类型值", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @PolymorphicToOne

用于多态一对一、一对多关联被关联的模型中使用，查询对应的左侧模型

允许出现多个，会根据`type`和`typeValue`判断，选择合适的注解配置查询模型。

**用法：**

`@PolymorphicToOne(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值")`

查询时指定字段：

`@PolymorphicToOne(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", fields={"指定查询时字段"})`

预加载，减少查询次数：

`@PolymorphicToOne(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", fields={"指定查询时字段"}, with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@PolymorphicToOne(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @PolymorphicToMany

用于多态多对多关联被关联的模型中使用，查询对应的左侧模型列表

允许出现多个，会根据`type`和`typeValue`判断，选择合适的注解配置查询模型。

**用法：**

`@PolymorphicToMany(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", limit=null, middleLeftField="中间表与模型类的关联字段", middleRightField="中间表与当前类的关联字段")`

查询时指定字段：

`@PolymorphicToMany(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"}, middleLeftField="中间表与模型类的关联字段", middleRightField="中间表与当前类的关联字段")`

预加载，减少查询次数：

`@PolymorphicToMany(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", order="排序规则字符串，例：age desc, id desc", fields={"指定查询时字段"}, middleLeftField="中间表与模型类的关联字段", middleRightField="中间表与当前类的关联字段", with=true, withFields={"指定序列化字段"})`

查询结果包含被软删除的数据：

`@PolymorphicToMany(model="关联模型", modelField="关联的模型用于关联的字段", field="当前模型用于关联的字段", type="多态类型字段名", typeValue="多态类型值", withSoftDelete=true)`

> 仅查询有效，非软删除模型请勿设置为 `true`

### @JoinFrom

一对一、一对多声明中，指定左表与右表关联用的字段名

**用法：**

`@JoinFrom("字段名")`

`@JoinFrom(field="字段名")`

### @JoinTo

一对一、一对多声明中，指定右表与左表关联用的字段名

**用法：**

`@JoinTo("字段名")`

`@JoinTo(field="字段名")`

### @JoinToMiddle

多对多声明中，指定左侧模型关联到中间表模型

**用法：**

`@JoinToMiddle(field="左侧关联字段", middleField="中间表关联字段")`

### @JoinFromMiddle

多对多声明中，指定中间表模型关联到右侧模型

**用法：**

`@JoinFromMiddle(field="右侧关联字段", middleField="中间表关联字段")`

### @AutoSelect

指定模型查询（find、select）时，该关联属性是否自动查询出关联数据。

不写该注解，或不指定值默认为true

**用法：**

`@AutoSelect`

`@AutoSelect(true)`

是否总是显示该属性，如果为false，在为null时序列化为数组或json不显示该属性。默认为true

`@AutoSelect(alwaysShow=false)`

### @AutoInsert

指定模型插入（insert、save）时，该关联属性是否自动插入关联数据。

不指定值默认为true

**用法：**

`@AutoInsert`

`@AutoInsert(true)`

`@AutoInsert(false)`

### @AutoUpdate

指定模型更新（update、save）时，该关联属性是否自动更新关联数据。

不指定值默认为true

当`orphanRemoval`为`true`时，会把不包含在模型里的关联数据删除后再更新、插入关联数据。

**用法：**

`@AutoUpdate`

`@AutoUpdate(true)`

`@AutoUpdate(false)`

`@AutoUpdate(orphanRemoval=true)`

### @AutoSave

指定模型保存（save）时，该关联属性是否自动保存关联数据。

不指定值默认为true

当`orphanRemoval`为`true`时，会把不包含在模型里的关联数据删除后再更新、插入关联数据。

**用法：**

`@AutoSave`

`@AutoSave(true)`

`@AutoSave(false)`

`@AutoSave(orphanRemoval=true)`

### @AutoDelete

指定模型删除（delete）时，该关联属性是否自动删除关联数据。

不指定值默认为true

**用法：**

`@AutoDelete`

`@AutoDelete(true)`

`@AutoDelete(false)`

## 方法

### 查询指定关联

使用关联模型时，你可以设置注解`@AutoSelect(false)`，让它不自动查询。在之后想要用到的时候，再手动查询。

```php
$model->queryRelations('属性名');

// 也可以支持多个
$model->queryRelations('属性名1', '属性名2');

// 查询后可以这么获取结果
$data = $model->属性名;
```

### 为一个列表查询指定关联

```php
\Imi\Model\Model::queryRelationsList($modelList);
```
