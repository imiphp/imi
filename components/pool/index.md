由于 Swoole 的常驻内存特性，所以 IMI 中实现了连接池。所有的数据库、Redis连接，都通过连接池去获取。

## 定义连接池

连接池的配置，可以写在项目配置文件中，也可以写在服务器配置文件中。

在配置文件中有一个`pools`节点，里面配置的是连接池。

同步池子仅在`task`进程使用，异步池子在`worker`进程使用。一般使用时无需自行判断在哪个进程，框架会自动帮你获取对应的连接。

### MySQL 连接池

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
					],
				],
				'resource' => [
					'dbClass' => \Imi\Db\Drivers\PdoMysql\Driver::class,
					'host' => '127.0.0.1',
					'user' => 'root',
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
					'dbClass'	=> \Imi\Db\Drivers\CoroutineMysql\Driver::class,
					'host'		=> '127.0.0.1',
					'user'		=> 'root',
					'password'	=> 'root',
					'database'	=> 'database',
					// 'timeout' => '建立连接超时时间',
					// 'charset' => '',
					// 'options' => [], // PDO连接选项
				],
			],
		]
	],
];
```

### Redis 连接池

```php
<?php
return [
	'pools' => [
        // 连接池名称
		'alias1' => [
            // 同步池子，task进程使用
			'sync' => [
				'pool' => [
					'class'		=>	\Imi\Redis\SyncRedisPool::class,
					'config' => [
						// 同上
					],
				],
				'resource' => [
					// 'host'	=>	'127.0.0.1',
					// 'port'	=>	6379,
					// 是否自动序列化变量
					// 'serialize'	=>	true,
					// 密码
					// 'password'	=>	null,
					// 第几个库
					// 'db'	=>	0,
				],
			],
            // 异步池子，worker进程使用
			'async' => [
				'pool'	=>	[
					'class'		=>	\Imi\Redis\CoroutineRedisPool::class,
					'config'	=>	[
						// 同上
					],
				],
				'resource'	=>	[
					// 同上
				],
			],
		]
	],
];
```

## 获取连接

## 手动释放连接

## 自动释放连接