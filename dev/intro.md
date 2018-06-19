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