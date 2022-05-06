# Phar 支持

[toc]

`imi v2.1.4` 已经实验性支持了 Phar 模式运行。

打包为 Phar 文件后，可以单文件部署非常方便，再配合 swoole-cli 可谓是如虎添翼。

## 安装

```bash
composer require --dev imiphp/imi-phar
```

## 快速开始

```bash

# 切换到需要打包的项目目录
cd ~/my-xxx-project

# 首次运行需初始化打包配置，将在项目目录下创建默认的`imi-phar-cfg.php`配置，建议把文件添加到代码仓库
vendor/bin/imi-phar build --init

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
        //   - '*'自动包含根目录除`vendor`以外的目录。（默认）
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
    // 由于 PHP 内核 BUG，该选项暂时屏蔽
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

## 默认过滤器

### 过滤规则

- 任何以点开头的文件都会被忽略，如果想这类型的文件，请使用`files`选项指定或自行提供`finder`实例。
- `vsc`目录都将被忽略`['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg']`。

### 内置过滤器(项目)

```php
// https://github.com/box-project/box/blob/e2cbc2424c0c4b97b626653c7f8ff8029282b9aa/src/Configuration/Configuration.php#L1478
// Remove build files
->notName('composer.json')
->notName('composer.lock')
->notName('Makefile')
->notName('Vagrantfile')
->notName('phpstan*.neon*')
->notName('infection*.json*')
->notName('humbug*.json*')
->notName('easy-coding-standard.neon*')
->notName('phpbench.json*')
->notName('phpcs.xml*')
->notName('psalm.xml*')
->notName('scoper.inc*')
->notName('box*.json*')
->notName('phpdoc*.xml*')
->notName('codecov.yml*')
->notName('Dockerfile')
->exclude('build')
->exclude('dist')
->exclude('example')
->exclude('examples')
// Remove documentation
->notName('*.md')
->notName('*.rst')
->notName('/^readme((?!\.php)(\..*+))?$/i')
->notName('/^upgrade((?!\.php)(\..*+))?$/i')
->notName('/^contributing((?!\.php)(\..*+))?$/i')
->notName('/^changelog((?!\.php)(\..*+))?$/i')
->notName('/^authors?((?!\.php)(\..*+))?$/i')
->notName('/^conduct((?!\.php)(\..*+))?$/i')
->notName('/^todo((?!\.php)(\..*+))?$/i')
->exclude('doc')
->exclude('docs')
->exclude('documentation')
// Remove backup files
->notName('*~')
->notName('*.back')
->notName('*.swp')
// Remove tests
->notName('*Test.php')
->exclude('test')
->exclude('Test')
->exclude('tests')
->exclude('Tests')
->notName('/phpunit.*\.xml(.dist)?/')
->notName('/behat.*\.yml(.dist)?/')
->exclude('spec')
->exclude('specs')
->exclude('features')
// Remove CI config
->exclude('travis')
->notName('travis.yml')
->notName('appveyor.yml')
->notName('build.xml*')
```

### 内置过滤器(vendor)

```php
->notName(['/LICENSE|.*\\.md|.*\\.dist|Makefile/', '*.macro.php'])
->exclude([
    'doc',
    'test',
    'test_old',
    'tests',
    'Tests',
    'vendor-bin',
    'vendor/bin',
]);
```

## 注意事项

- 不要在配置文件中，使用 `__DIR__`、`__FILE__` 等方式，指定物理路径，比如日志保存目录，而应该使用 `\Imi\App::get(\Imi\AppContexts::APP_PATH_PHYSICS)`

- 默认打包文件路径是 `build/imi.phar`，如果运行不要忘记把 .env 文件（如果有）复制进去
