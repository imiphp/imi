# ClassObject

**类名:** `Imi\Util\ClassObject`

类和对象相关工具类

## 方法

### isAnymous

是否是匿名类对象

```php
// true
var_dump(ClassObject::isAnymous(App::getBean('Logger')));

// false
var_dump(ClassObject::isAnymous(new \Imi\Log\Logger));
```
