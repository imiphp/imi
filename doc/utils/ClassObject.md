# ClassObject

[toc]

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

### 处理可能是同级的类名

如果 `$className` 是一个类名，则原样返回

否则返回 `$sameLevelClass` 同级下的类名

```php
ClassObject::parseSameLevelClassName($className, $sameLevelClass)
```

### 将方法的参数处理成 kv 数组

```php
class A
{
    public function test($id, $name, $age = 9999)
    {

    }
}

$args = [
    19260817,
    'imiphp.com',
];

$result1 = ClassObject::convertArgsToKV('A', 'test', $args, true);
var_dump($result1);
/*
array(3) {
  ["id"]=>
  int(19260817)
  ["name"]=>
  string(10) "imiphp.com"
  ["age"]=>
  int(9999)
}
*/

$result2 = ClassObject::convertArgsToKV('A', 'test', $args, false);
var_dump($result2);
/*
array(2) {
  ["id"]=>
  int(19260817)
  ["name"]=>
  string(10) "imiphp.com"
}
*/
```