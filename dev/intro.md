IMI 目前提供了命令行生成工具，方便开发者，减少重复无谓的体力劳动。

开发者工具的使用需要开发者自建php文件，内容如下：

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Imi\App;

App::runTool('ImiDemo\HttpDemo');
```

使用命令行工具：

```
php tool.php 工具名 参数
```