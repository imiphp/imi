# DateTime

**类名:** `Imi\Util\DateTime`

日期时间工具类

## 方法

### getSecondsByInterval

将一个 `\DateInterval`，与当前时间进行计算，获取毫秒数

```php
$d1 = new DateTime("2019-06-21");
$d2 = new DateTime("2018-06-21");
$diff = $d2->diff($d1); 
$s = \Imi\Util\DateTime::getSecondsByInterval($diff);
var_dump($s); // 31622400
```
