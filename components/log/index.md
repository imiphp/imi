# 日志

IMI 中的缓存，符合PSR-3规范。使用时需要先配置驱动和实例。

允许有多个同类型处理器实例，每个实例单独设置要处理的日志等级，非常灵活。

## 配置

### 控制台日志

```php
return [
	'beans'	=>	[
		'Logger'	=>	[
			'exHandlers'	=>	[
				[
					'class'		=>	\Imi\Log\Handler\File::class,
					'options'	=>	[
						/* 日志公共配置-开始 */
						// 允许记录的日志等级们
						'levels'	=>	\Imi\Log\LogLevel::ALL,
						// 日志缓存数量，当日志达到指定条数时，执行批量写入操作，减少对性能的影响，默认0，每次都写入
						// 'logCacheNumber'	=>	0,
						// 日志格式
						// 'format'	=>	'{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}',
						// 调用跟踪格式
						// 'traceFormat'	=>	'#{index}  {call} called at [{file}:{line}]',
						/* 日志公共配置-结束 */

						/**
						 * 要保存的文件名
						 * 支持使用date()函数中所有的格式，如经典年月日：logs/{y}-{m}-{d}.log
						 * 如果文件体积超出限制，会自动创建编号文件，如：第一个文件2018-01-01.log，第二个文件2018-01-01(1).log，第三个文件2018-01-01(2).log
						 */
						'fileName'	=>	dirname(__DIR__, 3) . '/log.log',
						// 单文件最大体积，单位字节，默认1G
						// 'maxSize'	=>	1073741824,
						// trace日志精简（实验性功能）
						'traceMinimum' => true,
					],
				]
			],
		],
	]
];
```

### 文件日志

```php
return [
	'beans'	=>	[
		'Logger'	=>	[
			'exHandlers'	=>	[
				[
					'class'		=>	\Imi\Log\Handler\File::class,
					'options'	=>	[
						// 支持同上的公共配置

						/**
						 * 要保存的文件名
						 * 支持使用date()函数中所有的格式，如经典年月日：logs/{y}-{m}-{d}.log
						 * 如果文件体积超出限制，会自动创建编号文件，如：第一个文件2018-01-01.log，第二个文件2018-01-01(1).log，第三个文件2018-01-01(2).log
						 */
						'fileName'	=>	dirname(__DIR__, 3) . '/log.log',
						// 单文件最大体积，单位字节，默认1G
						// 'maxSize'	=>	1073741824,
						
					],
				]
			],
		],
	]
];
```

### 框架默认日志处理

```php
return [
	'beans'	=>	[
		'Logger'	=>	[
			'coreHandlers'	=>	[
				// 这里配置，或者清空
			],
		],
	]
];
```

## 配置中可选变量说明

首先，所有支持传入`{xxx}`形式的格式中，都支持`date()`函数支持的格式，如`Y-m-d`等。

### format

`message` 日志消息
`level` 日志等级
`timestamp` 日志时间戳，精度：秒
`trace` 代码调用跟踪

### lastTraceFormat

参考`debug_backtrace()`函数返回字段。
`call` 调用跟踪

### traceFormat

`call` 调用跟踪
`index` 顺序

## 使用

```php
use Imi\Log\Log;
use Imi\Log\LogLevel;

Log::log(LogLevel::INFO, '日志内容');
// 第三个参数可选传入数据，也可代入上面的变量
Log::log(LogLevel::INFO, '日志内容', []);

// 第三个参数可选传入数据，也可代入上面的变量
Log::emergency('日志内容', []);

Log::alert('日志内容');
Log::critical('日志内容');
Log::error('日志内容');
Log::warning('日志内容');
Log::notice('日志内容');
Log::info('日志内容');
Log::debug('日志内容');
```