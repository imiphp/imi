# 数据库操作

数据库连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

## 连贯操作

IMI 中数据库查询连贯操作都来自于查询器，查询器的创建方式：

```php
use Imi\Db\Db;
$query = Db::query();
```

### 事务

```php
// 开启事务
Db::getInstance()->beginTransaction();
// 提交事务
Db::getInstance()->commit();
// 回滚事务
Db::getInstance()->rollBack();
```

**自动事务处理**

`@Transaction` 注解，类：`Imi\Db\Annotation\Transaction`

这个注解可以加在任意方法上，在方法调用前开启事务，在方法中抛出异常时回滚事务，方法成功返回时提交事务。

### 指定表 (table/from)

```php
// 指定表名
Db::query()->table('tb_test');
// 指定表名并且设置别名
Db::query()->table('tb_test', 'test');
// 指定表名和数据库名
Db::query()->table('tb_test', null, 'db1');
// 传入参数原样代入到SQL中
Db::query()->tableRaw('tb_test');
```

> `table()` 和 `tableRaw()` 也可以使用 `from()` 和 `fromRaw()` 代替。

### distinct

```php
// 结果唯一
Db::query()->distinct();
// 结果不唯一
Db::query()->distinct(false);
```

### 指定字段 (field)

```php
// 指定一个字段
Db::query()->field('id');

// 指定多个字段，也支持设置别名。空格和as都支持
Db::query()->field('id', 'name1 name', 'age1 as age');

// 和上面的用法等同
Db::query()->field(['id', 'name1 name', 'age1 as age']);

// 传入参数原样代入到SQL中
Db::query()->fieldRaw('id, name1 name, age1 as age');
```

### 条件 where

#### where

```php
// id = 1
Db::query()->where('id', '=', 1);

// id > 1
Db::query()->where('id', '>', 1);

// title like '%test%'
Db::query()->where('title', 'like', '%test%');

// id between 1 and 10
Db::query()->where('id', 'between', [1, 10]);

// id not between 1 and 10
Db::query()->where('id', 'not between', [1, 10]);

// or id = 1
Db::query()->where('id', '=', 1, 'or');
Db::query()->orWhere('id', '=', 1);
```

#### whereRaw

```php
// 传入参数原样代入到SQL中
Db::query()->whereRaw('id >= 1');
// 传入参数原样代入到SQL中，并且为or条件
Db::query()->whereRaw('id >= 1', 'or');
Db::query()->orWhereRaw('id >= 1');
```

#### whereBrackets

```php
// where id = 1 or (age < 14)
Db::query()->where('id', '=', 1)->whereBrackets(function(){
	// 直接返回字符串
	return 'age < 14';
}, 'or');

// where id = 1 or (age < 14)
Db::query()->where('id', '=', 1)->whereBrackets(function(){
	// 直接返回字符串
	return new \Imi\Db\Query\Where\Where('age', '<', 14);
}, 'or');
Db::query()->where('id', '=', 1)->orWhereBrackets(function(){
	// 直接返回字符串
	return new \Imi\Db\Query\Where\Where('age', '<', 14);
});
```

#### whereStruct

```php
// or age < 14
Db::query()->whereStruct(new \Imi\Db\Query\Where\Where('age', '<', 14), 'or');
Db::query()->orWhereStruct(new \Imi\Db\Query\Where\Where('age', '<', 14));
```

#### 其它

```php
// or age between 1 and 14
Db::query()->whereBetween('age', 1, 14, 'or');
Db::query()->orWhereBetween('age', 1, 14);

// or age not between 1 and 14
Db::query()->whereNotBetween('age', 1, 14, 'or');
Db::query()->orWhereNotBetween('age', 1, 14);

// or age in (1,2,3)
Db::query()->whereIn('age', [1, 2, 3], 'or');
Db::query()->orWhereIn('age', [1, 2, 3]);

// or age not in (1,2,3)
Db::query()->whereNotIn('age', [1, 2, 3], 'or');
Db::query()->orWhereNotIn('age', [1, 2, 3]);

// or age is null
Db::query()->whereIsNull('age', 'or');
Db::query()->orWhereIsNull('age');

// or age is not null
Db::query()->whereIsNotNull('age', 'or');
Db::query()->orWhereIsNotNull('age');
```

### join

```php
// select * from tb_test1 left join tb_test2 on tb_test1.aid = tb_test2.bid
Db::query()->table('tb_test1')->join('tb_test2', 'tb_test1.aid', '=', 'tb_test2.bid');
// select * from tb_test1 left join tb_test2 as test2 on tb_test1.aid = test2.bid
Db::query()->table('tb_test1')->join('tb_test2', 'tb_test1.aid', '=', 'test2.bid', 'test');
// select * from tb_test1 right join tb_test2 on tb_test1.aid = tb_test2.bid and tb_test1.age > 14
Db::query()->table('tb_test1')->join('tb_test2', 'tb_test1.aid', '=', 'tb_test2.bid', null, new \Imi\Db\Query\Where\Where('tb_test1.age', '>', '14'), 'right');

// select * from tb_test1 left join tb_test2 on tb_test1.aid = tb_test2.bid
Db::query()->table('tb_test1')->joinRaw('left join tb_test2 on tb_test1.aid = tb_test2.bid');

// 下面三种用法，第5个参数都支持传Where
// left join
Db::query()->table('tb_test1')->leftJoin('tb_test2', 'tb_test1.aid', '=', 'tb_test2.bid');
// right join
Db::query()->table('tb_test1')->rightJoin('tb_test2', 'tb_test1.aid', '=', 'tb_test2.bid');
// cross join
Db::query()->table('tb_test1')->crossJoin('tb_test2', 'tb_test1.aid', '=', 'tb_test2.bid');
```

### order

```php
// order by id asc, age desc
Db::query()->order('id')->order('age', 'desc');

// order by id desc
Db::query()->orderRaw('id desc');
```

### group by

```php
// group by id, name
Db::query()->group('id', 'name');

// group by sum(id)
Db::query()->groupRaw('sum(id)');
```

### having

`having()`用法同`where()`

`havingRaw()`用法同`whereRaw()`

`havingBrackets()`用法同`whereBrackets()`

`havingStruct()`用法同`whereStruct()`

### 分页查询

```php
// limit 900, 100
Db::query()->page(10, 100);

// limit 10, 1
Db::query()->offset(10)->limit(1);

// limit 1
Db::query()->limit(1);
```

## 查询执行

### 查询记录

#### 查询单行

```php
$result = Db::query()->table('tb_test')->select();
$result->get(); // 数组
$result->get($className); // $className对应的类对象
```

#### 查询多行

```php
$result = Db::query()->table('tb_test')->select();
$result->getArray(); // 数组内嵌套数组
$result->getArray($className); // 数组内嵌套$className对应的类对象
$result->getRowCount(); // 获取查询出的记录行数
```

#### 聚合函数

```php
// count(*)
Db::query()->table('tb_test')->count();

// count(id)
Db::query()->table('tb_test')->count('id');

// sum(id)
Db::query()->table('tb_test')->sum('id');

// avg(id)
Db::query()->table('tb_test')->avg('id');

// max(id)
Db::query()->table('tb_test')->max('id');

// min(id)
Db::query()->table('tb_test')->min('id');

// 其它自定义：test(id)
Db::query()->table('tb_test')->aggregate('test', 'id');
```

### 插入记录

```php
// insert into tb_test(name, age) values('yurun', 666)
$result = Db::query()->table('tb_test')->insert([
	'name'	=>	'yurun',
	'age'	=>	666,
]);

// insert into tb_test values('yurun', 666)
$result = Db::query()->table('tb_test')->insert([
	'yurun',666,
]);

$result->isSuccess(); // SQL是否执行成功
$result->getLastInsertId(); // 获取最后插入的ID
$result->getAffectedRows(); // 获取影响行数
```

### 更新记录

```php
// update tb_test set name = 'yurun', age = 666 where id = 1
$result = Db::query()->table('tb_test')->where('id', '=', 1)->update([
	'name'	=>	'yurun',
	'age'	=>	666,
]);

// $result使用方法同上
```

### 删除记录

```php
// delete from tb_test where id = 1
$result = Db::query()->table('tb_test')->where('id', '=', 1)->delete();

// $result使用方法同上
```

## 直接执行SQL

```php
$result = Db::query()->execute('select * from tb_test'));

// $result使用方法同上
```

## 参数绑定

复杂的查询时，难免需要拼接 SQL，这时候就需要参数绑定来防止注入了！

```php
// where id = 123
$result = Db::query()->whereRaw('id = ?')->bindValue(1, 123);
$result = Db::query()->whereRaw('id = :val')->bindValue(':val', 123);

// 批量绑定
$result = Db::query()->bindValues([
	':name'	=>	'yurun',
	':age'	=>	666,
])->execute('select * from tb_test where name = :name and age = :age');
```