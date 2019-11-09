# Args

**类名:** `Imi\Util\Args`

命令行参数操作类

## 方法

### get

获取值

```php
// 不妨运行加一个 -abc 123 参数看看效果
var_dump(Args::get('abc'));

// 支持默认值
var_dump(Args::get('abc', 'default'));
```

### exists

指定数据是否存在

```php
var_dump(Args::exists('abc'));
```
