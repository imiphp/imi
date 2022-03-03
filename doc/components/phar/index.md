# Phar 支持

`imi 2.1` 已经实验性支持了 Phar 模式运行。

## 安装

```bash
composer require --dev imiphp/imi-phar
```

## 快速开始

```bash

# 切换到需要打包的项目目录
cd ~/my-xxx-project

# 首次运行需初始化打包配置，将在项目目录下创建默认的`imi-phar-cfg.php`配置，建议把文件添加到代码仓库
vendor/bin/imi-phar --init

# 执行打包命令，`xxx`为要打包容器，内置`swoole、workerman、roadrunner`容器支持，更多细节查看配置说明
vendor/bin/imi-phar build xxx

# 查看目录下的`build`目录获得打包的可执行`phar`文件
./build/imi.phar

```

更多命令行细节执行`vendor/bin/imi-phar -h`参考。

## 打包特性

打包后将初始化一些工作常量，详情查看 [内置常量列表](../../core/consts.html)

## 配置说明

该配置文件中所声明的目录或文件，无特殊说明均是基于该配置所在工作目录中的相对路径，请勿填写绝对路径。
如果文档中的配置与`vendor/bin/imi-phar --init`生成的配置文件不一致，建议以生成的配置为准。
配置文件中用到的文件过滤器使用规则均由`symfony/finder`提供，更多细节可看 [finder doc](https://symfony.com/doc/current/components/finder.html)。

```php
[
    // 构建产物
    'output'       => 'build/imi.phar',

    // 参与打包的文件
    // 可选值：
    //   - '*'自动包含根目录下的 *.php、*.macro 文件。（默认）
    //   - 空数组不包含任何文件。
    //   - 定义数组并填入文件名（仅限于当前目录下的文件）。
    'files'        => '*',

    // 参与打包的目录
    'dirs'         => [
        // 参与打包的目录
        // 可选值：
        //   - '*'自动包含根目录除`vendor`以为的目录。（默认）
        //   - 空数组不包含任何目录。
        //   - 定义数组并填入目录名名（仅限于当前目录下的目录名）。
        'in' => '*',
        // 要排除的的目录，仅对 dirs 扫描到的内容有作用。（使用参考 symfony/finder->exclude）
        'excludeDirs'  => [],
        // 要排除的的文件，仅对 dirs 扫描到的内容有作用。（使用参考 symfony/finder->notName）
        'excludeFiles' => [],
    ],

    // 包含 vendor 目录
    // 可选值：
    //   - true  : 打包 vendor 中的文件（默认）
    //   - false : 不打包 vendor 中的文件
    //   - symfony/finder 实例 : 完全自定义如何获得文件
    // 无特殊情况勿动
    'vendorScan'   => true,

    // 传入 symfony/finder 实例，支持多个，完全自定义扫描的内容。
    'finder' => [],

    // 是否转存构建时的 git 信息
    'dumpGitInfo' => true,

    // 自定义启动入口
    // 如果该处提供入口参数，命令行打包时可不传入
    // 如果命令行输入参数`container`，则覆盖该选项值
    // 如果指定一个有效的 php 文件文件则可以完全控制入口
    // 可选值：
    //   - swoole
    //   - workerman
    //   - roadrunner
    //   - 当前目录下的一个有效 php 文件
    'bootstrap' => null,

    // 压缩算法，一旦启用压缩，则执行环境也必须加载对应的依赖库
    // 由于兼容性问题，该选项暂时屏蔽
    // 可选值：
    //   - \Phar::NONE : 不压缩
    //   - \Phar::GZ   : 必须启用扩展 zlib
    //   - \Phar::BZ2  : 必须启用扩展 bzip2
    'compression' => \Phar::NONE,
]
```

## finder 的使用

支持在配置文件中支持声明`finder`实例数组。

例子，禁用内置文件扫描器，提供一个忽略`png`与`pdf`的文件扫描器：
```php
return [
[
    'output'       => 'build/imi.phar',
    'files'        => [],
    'dirs'         => [
        'in' => [],
        'excludeDirs'  => [],
        'excludeFiles' => [],
    ],
    'vendorScan'   => false,
    'finder' => [
        (new Symfony\Component\Finder\Finder())
            ->in(__DIR__)
            ->notName(['*.png', '*.pdf'])
            ->files()
    ],
]
```
