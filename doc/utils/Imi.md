# Imi

[toc]

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

### getClassNamespace

获取类命名空间

```php
// A\B
echo Imi::getClassNamespace('A\B\C');
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
// /mnt/d/projects/imi-demo/vendor/imiphp/imi/src/
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
// php /mnt/d/projects/imi-demo/HttpDemo/bin/imi-swoole swoole/reload
echo Imi::getImiCmd('swoole', 'reload');
```

### getRuntimePath

获取运行时目录路径

```php
// 返回：运行时目录路径
echo Imi::getRuntimePath();

// 返回：运行时目录路径/a.jpg
echo Imi::getRuntimePath('a.jpg');

// 返回：运行时目录路径/a/b.jpg
echo Imi::getRuntimePath('a', 'b.jpg');
```

### getModeRuntimePath

获取模式对应的运行时目录路径

```php
// 返回：运行时目录路径/swoole/a/b.jpg
echo Imi::getModeRuntimePath('swoole', 'a', 'b.jpg');
```

### getCurrentModeRuntimePath

获取当前模式对应的运行时目录路径

```php
// 返回：运行时目录路径/swoole/a/b.jpg
echo Imi::getModeRuntimePath('a', 'b.jpg');
```

### buildRuntime

构建运行时缓存

```php
/**
 * 构建运行时缓存.
 *
 * @param string|null $cacheName 如果为空则默认为runtime
 */
public static function buildRuntime(?string $cacheName = null): void
```

### loadRuntimeInfo

```php
/**
 * 从文件加载运行时数据
 */
public static function loadRuntimeInfo(string $cacheName): bool
```

### incrUpdateRuntime

```php
/**
 * 增量更新运行时缓存.
 */
public static function incrUpdateRuntime(array $files): void
```

### checkReusePort

```php
/**
 * 检查系统是否支持端口重用.
 */
public static function checkReusePort(): bool
```

### eval

`eval()` 函数的安全替代方法

```php
Imi::eval('echo "hello imi";');
```

### isWSL

检测是否为 WSL 环境

```php
/**
 * 检测是否为 WSL 环境.
 */
public static function isWSL(): bool
```

### getLinuxVersion

获取 Linux 版本号

```php
/**
 * 获取 Linux 版本号.
 */
public static function getLinuxVersion(): string
```

### getDarwinVersion

获取苹果系统版本

```php
/**
 * 获取苹果系统版本.
 */
public static function getDarwinVersion(): string
```

### getCygwinVersion

```php
/**
 * 获取 Cygwin 版本.
 */
public static function getCygwinVersion(): string
```

### isDockerEnvironment

判断是否为 Docker 环境

```php
/**
 * 判断是否为 Docker 环境.
 */
public static function isDockerEnvironment(): bool
```

### checkAppType

检查应用运行类型

```php
/**
 * 检查应用运行类型.
 */
public static function checkAppType(string $appType): bool
```

> 目前支持：swoole、workerman、fpm、cli
