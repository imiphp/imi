# Bit

**类名:** `Imi\Util\Bit`

位操作工具类

## 方法

### has

判断是否包含值

```php
// true
var_dump(Bit::has(LOCK_SH | LOCK_NB, LOCK_NB));

// false
var_dump(Bit::has(LOCK_SH, LOCK_NB));
```