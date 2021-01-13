# Coroutine

**类名:** `Imi\Swoole\Util\Coroutine`

协程帮助类

继承了`\Swoole\Coroutine`

## 方法

### isIn

判断当前是否在协程中运行

```php
var_dump(Coroutine::isIn());
```

> 由于继承了 Swoole 的协程类，所以可以使用其全部方法
