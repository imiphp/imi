# 宏定义

支持在 imi 框架中使用宏定义。

支持在 PHP 代码中使用类似 C/C++ 中的宏，进行代码预编译。

可以方便兼容不同版本和环境下运行的 PHP 代码。

在一些场景可以通过预定义常量，来提升性能，减少运行时判断。

本组件基于 [Yurunsoft/php-macro](https://github.com/Yurunsoft/php-macro) 组件，该组件由宇润主导开发。

> 这是 imi v2.1.0 引入的实验性新特性

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-macro": "~2.1.0"
    }
}
```

### 支持的宏

#### 文件格式

支持的文件名：`*.php`、`*.macro`

**.php**

需要在文件中任意位置顶格加入代码：

```php
# macro
```

> 该代码不加不生效

`A.php` 执行时，会在相同目录生成处理后的 `A.php.macro.php`。

**.macro**

需要创建同名的 `.php` 文件，如 `A.php`，内容为空即可。

再创建 `A.macro` 编写 PHP 代码。

执行时，会在相同目录生成处理后的 `A.macro.php`。

#### 常量

**宏：**`#define`、`#const`、`#ifdef`、`#ifndef`

**例子：**

```php
#ifndef IN_SWOOLE
    # define IN_SWOOLE extension_loaded('swoole')
#endif
#ifdef IN_SWOOLE
#if IN_SWOOLE
\Co\run(function(){
    echo 'hello world';
});
#endif
#endif
```

> 注意：使用宏定义的常量，仅在生成代码时有效，运行时无效

#### 条件语句

**宏：**`#if`、`#else`、`#elif`、`#endif`

**例子：**

```php
<?php
#if version_compare(\PHP_VERSION, '8.0', '>=')
function test(): string|false
#else
/**
 * @return string|false
 */
function test()
#endif
{
    return 'hello world';
}
```

PHP >= 8.0 环境下生成的代码：

```php
<?php
function test(): string|false
{
    return 'hello world';
}
```

PHP < 8.0 环境下生成的代码：

```php
<?php
/**
 * @return string|false
 */
function test()
{
    return 'hello world';
}
```
