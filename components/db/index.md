# 数据库

数据库连接池配置方式已经在连接池里讲过，这里就不重复了，直接说使用方法。

## 连接池配置

```php
<?php
return [
	'pools' => [
        // 连接池名称
		'alias1' => [
            // 同步池子，task进程使用
			'sync' => [
				'pool' => [
					'class' => \Imi\Db\Pool\SyncDbPool::class,
					'config' => [
                        // 池子中最多资源数
						// 'maxResources' => 10,
						// 池子中最少资源数
						// 'minResources' => 2,
						// 资源回收时间间隔，单位：秒
						// 'gcInterval' => 60,
						// 获取资源最大存活时间，单位：秒
						// 'maxActiveTime' => 3600,
						// 等待资源最大超时时间，单位：毫秒
						// 'waitTimeout' => 3000,
						// 负载均衡-轮流
						// 'resourceConfigMode' => ResourceConfigMode::TURN,
						// 负载均衡-随机
						// 'resourceConfigMode' => ResourceConfigMode::RANDOM,
					],
				],
				'resource' => [
					'host' => '127.0.0.1',
					'username' => 'root',
					'password' => 'root',
					'database' => 'database',
					// 'port'    => '3306',
					// 'timeout' => '建立连接超时时间',
					// 'charset' => '',
					// 'strict_type' => false, //开启严格模式，返回的字段将自动转为数字类型
				],
			],
            // 异步池子，worker进程使用
			'async' => [
				'pool'	=>	[
					'class'		=>	\Imi\Db\Pool\CoroutineDbPool::class,
					'config'	=>	[
						// 同上
					],
				],
				// resource也可以定义多个连接
				'resource'	=>	[
					[
						'host'		=> '127.0.0.1',
						'username'		=> 'root',
						'password'	=> 'root',
						'database'	=> 'database',
						// 'timeout' => '建立连接超时时间',
						// 'charset' => '',
						// 'options' => [], // PDO连接选项
					],
					[
						'host'		=> '127.0.0.2',
						'username'		=> 'root',
						'password'	=> 'root',
						'database'	=> 'database',
						// 'timeout' => '建立连接超时时间',
						// 'charset' => '',
						// 'options' => [], // PDO连接选项
					]
				],
			],
		],
		// 从库配置
		// 原连接池名后加.slave即为从库配置，非必设
		// 如果配置了，默认查询走从库，增删改走主库
		// 如果在事务中，默认都走主库
		'alias1.slave' => [
            // 同步池子，task进程使用
			'sync' => [
				'pool' => [
					'class' => \Imi\Db\Pool\SyncDbPool::class,
					'config' => [
                        // 池子中最多资源数
						// 'maxResources' => 10,
						// 池子中最少资源数
						// 'minResources' => 2,
						// 资源回收时间间隔，单位：秒
						// 'gcInterval' => 60,
						// 获取资源最大存活时间，单位：秒
						// 'maxActiveTime' => 3600,
						// 等待资源最大超时时间，单位：毫秒
						// 'waitTimeout' => 3000,
					],
				],
				'resource' => [
					'host' => '127.0.0.1',
					'username' => 'root',
					'password' => 'root',
					'database' => 'database',
					// 'port'    => '3306',
					// 'timeout' => '建立连接超时时间',
					// 'charset' => '',
					// 'strict_type' => false, //开启严格模式，返回的字段将自动转为数字类型
				],
			],
            // 异步池子，worker进程使用
			'async' => [
				'pool'	=>	[
					'class'		=>	\Imi\Db\Pool\CoroutineDbPool::class,
					'config'	=>	[
						// 同上
					],
				],
				'resource'	=>	[
					'host'		=> '127.0.0.1',
					'username'		=> 'root',
					'password'	=> 'root',
					'database'	=> 'database',
					// 'timeout' => '建立连接超时时间',
					// 'charset' => '',
					// 'options' => [], // PDO连接选项
				],
				// uri 写法
				// 'resource'  =>  [
                //     'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
                //     'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
                // ],
                // 'resource'  =>  'tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60;tcp://192.168.0.222/?username=root&password=root&database=db_test&timeout=60',
			],
		]
	],
];
```

## 直接操作 Db 对象

```php
// 获取新的数据库连接实例
$db = Db::getNewInstance();
// 读库
$db = Db::getNewInstance($poolName, QueryType::READ);
// 写库
$db = Db::getNewInstance($poolName, QueryType::WRITE);

// 获取数据库连接实例，每个RequestContext中共用一个
$db = Db::getInstance();
// 读库
$db = Db::getInstance($poolName, QueryType::READ);
// 写库
$db = Db::getInstance($poolName, QueryType::WRITE);

// 释放连接，回归连接池
Db::release($db);

$returnValue = Db::use(function(IDb $db){
	// 操作 $db
	return 'imi';
}); // imi
```

## 连贯操作

IMI 中数据库查询连贯操作都来自于查询器，查询器的创建方式：

```php
use Imi\Db\Db;
$query = Db::query();
```

### 事务

手动控制事务：

```php
// 开启事务
Db::getInstance()->beginTransaction();
// 提交事务
Db::getInstance()->commit();
// 回滚事务
Db::getInstance()->rollBack();
```

获取连接顺带自动开启/提交/回滚事务：

```php
Db::transUse(function(IDb $db){

});
Db::transUse(function(IDb $db){

}, $poolName, QueryType::WRITE);
```

获取连接后，想要使用某个连接，执行事务操作，自动开启/提交/回滚事务：

```php
$db = Db::getInstance();
Db::trans($db, function(IDb $db){

});
```

**自动事务处理**

`@Transaction` 注解，类：`Imi\Db\Annotation\Transaction`

这个注解可以加在任意方法上，在方法调用前开启事务，在方法中抛出异常时回滚事务，方法成功返回时提交事务。

`@Transaction`

`@Transaction(autoCommit="自动提交事务true/false，默认为true")`

事务类型：

事务嵌套（默认）

`@Transaction(type=TransactionType::NESTING)`

该方法必须在事务中被调用

`@Transaction(type=TransactionType::REQUIREMENT)`

如果当前不在事务中则开启事务

`@Transaction(type=TransactionType::AUTO)`

部分回滚：

`@Transaction(rollbackType=RollbackType::PART, rollbackLevels="回滚层数，默认为1")`

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

#### TP 风格 where

```php
// select * from `tb_test` where (`id` = 1 or (`id` = 2 ) and `title` like '%test%' and `age` > 18 and (`id` = 2 or (`id` = 3 ) ) )
Db::query()->from('tb_test')->whereEx([
	'id'	=>	1,
	'or'	=>	[
		'id'	=>	2,
	],
	'title'	=>	['like', '%test%'],
	'age'	=>	['>', 18],
	'and'   =>  [
		'id'    =>  2,
		'or'    =>  [
			'id'    =>  3,
		]
	]
]);
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

#### JSON 字段支持

看如下代码，`data`为`json`类型字段，查询`json`对象中的`member.age`为`11`的记录

```php
Db::query()->where('data->member.age', '=', 11)->select();
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

### 批量插入

```php
$result = Db::query()->from('test')->batchInsert([
	['name'=>'a'],
	['name'=>'b'],
	['name'=>'c'],
]);
$result->isSuccess(); // SQL是否执行成功
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

### 替换数据

```php
// replace into tb_test set id = 1, name = 'yurun', age = 666
$result = Db::query()->table('tb_test')->replace([
	'id'	=>	1,
	'name'	=>	'yurun',
	'age'	=>	666,
]);
```

### 递增/递减

```php
// score 递增 1
$result = Db::query()->table('tb_test')
                     ->where('id', '=', 1)
					 ->setFieldInc('score')
					 ->update();

// score 递增 10
$result = Db::query()->table('tb_test')
                     ->where('id', '=', 1)
					 ->setFieldInc('score', 10)
					 ->update();

// score 递减 1
$result = Db::query()->table('tb_test')
                     ->where('id', '=', 1)
					 ->setFieldDec('score')
					 ->update();

// score 递减 10
$result = Db::query()->table('tb_test')
                     ->where('id', '=', 1)
                     ->setFieldDec('score', 10)
					 ->update();
```

### update/insert/replace时使用表达式

```php
// update tb_test set score = score + 1 where id = 1
$result = Db::query()->table('tb_test')
                     ->where('id', '=', 1)
                     ->setFieldExp('score', 'score + 1')
					 ->update();
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

## Result 用法

```php
$result = Db::query()->table('tb_test')->select();
```

### 是否执行成功

```php
$success = $result->isSuccess(); // true/false
```

### 获取最后插入的ID

用于获取新增记录的自增字段值

```php
$lastInsertId = $result->getLastInsertId(); // int
```

### 获取影响行数

```php
$rows = $result->getAffectedRows();
```

> update 时，如果没有值被改变，可能会返回0

### 返回一行数据

#### 返回数组

```php
$dataArray = $result->get();
```

#### 返回对象

实例化这个类时，把数组传入构造方法

```php
$dataArray = $result->get(XXXModel::class);
```

### 返回数组列表

#### 成员为数组

```php
$list = $result->getArray();
```

#### 成员为对象

```php
$list = $result->getArray(XXXModel::class);
```

### 获取一列

```php
$ids = Db::query()->field('id')->select()->getColumn();
// 结果格式：[1, 2, 3]
```

### 获取标量结果

```php
$name = Db::query()->field('name')->where('id', '=', 1)->select()->getScalar();
```

### 获取记录行数

得到取回多少条记录

```php
$rowCount = $result->getRowCount();
```

### 获取执行的SQL

```php
$sql = $result->getSql();
```

### 获取结果集

```php
$statement = $result->getStatement(); // \Imi\Db\Interfaces\IStatement
```
