# 前置和后置事件

[toc]

在 imi 中，使用模型关联时，提供了监听前置和后置事件的方法，让开发者可以更加灵活地做一些自定义处理。

## 事件

### 插入事件

**前置事件名：** `IMI.MODEL.RELATION.INSERT.模型类名.模型属性名.BEFORE`

**后置事件名：** `IMI.MODEL.RELATION.INSERT.模型类名.模型属性名.AFTER`

**事件参数：**

| 参数名 | 类型 | 描述 |
| ------ | ------ | ------ |
| model | `string` | 模型类名 |
| propertyName | `string` | 模型属性名 |
| annotation | `OneToOne OneToMany ManyToMany PolymorphicOneToOne PolymorphicOneToMany PolymorphicManyToMany PolymorphicToOne PolymorphicToMany` | 注解，命名空间前缀`\Imi\Model\Annotation\Relation\` |
| struct | `OneToOne OneToMany ManyToMany PolymorphicOneToOne PolymorphicOneToMany PolymorphicManyToMany` | 用于获取一些信息的结构，命名空间前缀`\Imi\Model\Relation\Struct\` |

### 更新事件

**前置事件名：** `IMI.MODEL.RELATION.UPDATE.模型类名.模型属性名.BEFORE`

**后置事件名：** `IMI.MODEL.RELATION.UPDATE.模型类名.模型属性名.AFTER`

**事件参数：**

> 同插入事件参数

### 删除事件

**前置事件名：** `IMI.MODEL.RELATION.DELETE.模型类名.模型属性名.BEFORE`

**后置事件名：** `IMI.MODEL.RELATION.DELETE.模型类名.模型属性名.AFTER`

**事件参数：**

> 同插入事件参数

### 查询事件

**前置事件名：** `IMI.MODEL.RELATION.QUERY.模型类名.模型属性名.BEFORE`

**后置事件名：** `IMI.MODEL.RELATION.QUERY.模型类名.模型属性名.AFTER`

**事件参数：**

| 参数名 | 类型 | 描述 |
| ------ | ------ | ------ |
| model | `string` | 模型类名 |
| propertyName | `string` | 模型属性名 |
| annotation | `OneToOne OneToMany ManyToMany PolymorphicOneToOne PolymorphicOneToMany PolymorphicManyToMany PolymorphicToOne PolymorphicToMany` | 注解，命名空间前缀`\Imi\Model\Annotation\Relation\` |
| struct | `OneToOne OneToMany ManyToMany PolymorphicOneToOne PolymorphicOneToMany PolymorphicManyToMany` | 用于获取一些信息的结构，命名空间前缀`\Imi\Model\Relation\Struct\`。`annotation`为`PolymorphicToOne`时不会有该参数。 |
| query | `\Imi\Db\Query\Interfaces\IQuery` | 查询构建器对象，为前置事件时必传 |
