# Imi

**类名:** `Imi\Util\Imi`

框架里杂七杂八的各种工具方法基本都放在这个类

## 方法

仅列出可能常用的方法，大多数方法无需了解。

### parseDotRule

处理按.分隔的规则文本，支持\.转义不分隔

```php
// ['a', 'b', 'c']
var_dump(Imi::parseDotRule('a.b.c'));

// ['a.b', 'c']
var_dump(Imi::parseDotRule('a\.b.c'));
```

### getClassShortName

获取类短名称

```php
// C
echo Imi::getClassShortName('A\B\C');
```

### getNamespacePath

根据命名空间获取真实路径，返回null则为获取失败

```php
// /mnt/d/projects/imi-demo/vendor/yurunsoft/imi/src/
echo Imi::getNamespacePath('Imi'), PHP_EOL;

// /mnt/d/projects/imi-demo/HttpDemo/
echo Imi::getNamespacePath('ImiDemo\HttpDemo'), PHP_EOL;
```

### getNamespacePaths

根据命名空间获取真实路径，允许返回多个

```php
var_dump(Imi::getNamespacePaths('Imi'));
```

### getClassPropertyValue

获取类属性的值，值为beans配置或默认配置，支持传入Bean名称

构造方法赋值无法取出

```php
// 默认为Imi\Server\Group\Handler\Redis
echo Imi::getClassPropertyValue('ServerGroup', 'groupHandler');
```

### getImiCmd

获取imi命令行

```php
// php /mnt/d/projects/imi-demo/HttpDemo/bin/imi server/reload
echo Imi::getImiCmd('server', 'reload');
```
