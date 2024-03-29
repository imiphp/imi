# 枚举

[toc]

由于 PHP < 8.1 本身不支持枚举类型，imi 特别基于注解实现了枚举类。

> 此功能在 3.0 不会被内置，需要手动安装 `imi-old-enum` 组件。强烈建议使用 PHP 原生枚举！

## 安装

`composer require imiphp/imi-old-enum:~3.0.0`

## 枚举类定义

继承`Imi\Enum\BaseEnum`类，然后在类常量上面写上注解`EnumItem`，参数是当前常量的文字说明。

```php
<?php
namespace ImiDemo\HttpDemo\MainServer\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

class Status extends BaseEnum
{
    #[EnumItem(text: '正确')]
    const YES = 1;

    #[EnumItem(text: '错误')]
    const NO = 0;
}
```

## 使用

```php
// 根据名称获得常量值
Status::getValue('YES');

// 根据值获得文字说明
Status::getText(Status::YES);

// 根据值获得 EnumItem 注解对象
Status::getData(Status::YES);

// 获取枚举类中所有名称
Status::getNames();

// 获取枚举类中所有值
Status::getValues();

// 验证值是否合法;true/false
Status::validate(Status::YES);

// 验证值断言，值不合法会抛出异常: \InvalidArgumentException
Status::assert(Status::YES);
```
