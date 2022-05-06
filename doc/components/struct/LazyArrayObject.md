# LazyArrayObject

[toc]

同时可以作为数组和对象访问的类

```php
// $object = new \Imi\Util\LazyArrayObject;
$object = new \Imi\Util\LazyArrayObject([
    'id'    =>  123,
    'name'  =>  'imi',
]);

$object['id'] = 111;
echo 'id:', $object->id, PHP_EOL;

$object->id = 222;
echo 'id:', $object['id'], PHP_EOL;

$arrayData = $object->toArray();
var_dump($arrayData);
```
