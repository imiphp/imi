# ORM

## 介绍

imi 中目前支持两个模型：数据库模型、内存表模型

数据库模型与传统 `php-fpm` 框架下使用并无多大差别，甚至更加好用。

在 imi 框架中，提供了一个模型生成工具。使用该工具生成的模型，在使用时无需手动定义字段，支持 IDE 代码提示。

模型生成命令使用说明：<https://doc.imiphp.com/v2.1/dev/generate/model.html>

## 模型字段和序列化

模型实例对象可以同时作为数组和对象使用。

如数据库中字段为`user_age`，同时支持原字段名和驼峰命名，使用方法如下：

```php
// 下面三句是等同的
$model['user_age'] = 1;
$model['userAge'] = 1;
$model->setUserAge(1);
```

调用`$model->toArray()`可以转为数组。可以直接对模型进行`json_encode()`处理。

模型序列化后的字段默认为驼峰格式，如：数据库字段为`user_age`，序列化后为`userAge`。

可以使用注解，设置为使用原数据库字段名：`@Entity(camel=false)`