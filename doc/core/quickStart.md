# 单文件运行 imi（快速启动）

imi v2.1.7 开始支持了单文件运行 imi。

## 使用场景

需要在 imi 环境中，单独执行一段代码。

## 使用方法

在项目下创建 `test.php`：

```php
<?php

declare(strict_types=1);

use Imi\App;
use Imi\Cli\CliApp;

// 加载 autoload.php 必不可少
require __DIR__ . '/vendor/autoload.php';

// 第一个参数传入项目根目录
// 第二个参数是各种模式的 App 类。CliApp 也可以换成 SwooleApp 等类。
App::runApp(__DIR__, CliApp::class, function () {
    // 你自己的代码写在这
    echo 'Test quick start', \PHP_EOL;
});
```

**Swoole 环境使用：**

```php

use Imi\Swoole\SwooleApp;
use function Swoole\Coroutine\run;

// 加载 autoload.php 必不可少
require __DIR__ . '/vendor/autoload.php';

run(function () {
    App::runApp(__DIR__, SwooleApp::class, function () {
        echo 'Test swoole quick start', \PHP_EOL;
    });
});
```

直接执行：`php test.php`

正常输出：

```log
Test quick start
```
