# SQL 监听

## 说明

要使用 SQL 监听功能，必须先开启，并且不推荐在生产环境使用。

开启方式是在配置文件中的 `beans` 中加入：

```php
'DbQueryLog' => [
    'enable' => true,
]
```

## 事件

### SQL 执行

事件名：`IMI.DB.EXECUTE`

每一个 SQL 语句执行后都会触发该事件。

示例：

```php
<?php

namespace Imi\Test\Component\Db\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Db\Event\Param\DbExecuteEventParam;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;

/**
 * @Listener("IMI.DB.EXECUTE")
 */
class DbExecuteListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param DbExecuteEventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e): void
    {
        if ($e->throwable)
        {
            Log::error(sprintf('[%s] %s', $e->throwable->getMessage(), $e->sql));
        }
        else
        {
            Log::info(sprintf('[%ss] %s', round($e->time, 3), $e->sql));
        }
    }
}
```

### 准备 SQL 语句

事件名：`IMI.DB.PREPARE`

每一个 SQL 语句准备后都会触发该事件。

示例：

```php
<?php

namespace Imi\Test\Component\Db\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Db\Event\Param\DbPrepareEventParam;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;

/**
 * @Listener("IMI.DB.PREPARE")
 */
class DbPrepareListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param DbPrepareEventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e): void
    {
        if ($e->throwable)
        {
            Log::error(sprintf('[%s] %s', $e->throwable->getMessage(), $e->sql));
        }
        else
        {
            Log::info(sprintf('[prepare] %s', $e->sql));
        }
    }
}
```
