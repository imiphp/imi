# 日志

imi 中的缓存，符合PSR-3规范。使用时需要先配置驱动和实例。

允许有多个同类型处理器实例，每个实例单独设置要处理的日志等级，非常灵活。

imi v2.x 开始采用了 monolog 作为日志组件，废弃了 v1 中的配置方式，改用全新的配置方式，请知悉！ 

## 配置

```php
// 日志配置
'logger' => [
    // 'default' => 'imi', // 默认 channel
    'async' => false, // 是否启用异步日志，仅 Swoole 模式有效，可以有效提升大量日志记录时的接口响应速度
    'asyncQueueLength' => 1024, // 异步日志队列长度
    'channels' => [
        'imi' => [
            'handlers' => [
                // 命令行配置，仅 Swoole、Workerman 推荐
                [
                    'class'     => \Imi\Log\Handler\ConsoleHandler::class,
                    // 'env' => ['swoole'], // 允许的运行环境，只有是数组内的运行环境才有效，默认为 null 则不限制
                    // 日志 Handler 构造方法参数，具体参数详见该类构造方法
                    'construct' => [
                        'level'  => \Imi\Log\MonoLogger::DEBUG, // 开发调试环境
                        // 'level'  => \Imi\Log\MonoLogger::INFO,  // 生产环境
                        'bubble' => true,
                    ],
                    'formatter' => [
                        'class'     => \Imi\Log\Formatter\ConsoleLineFormatter::class,
                        'construct' => [
                            'format'                     => null,
                            'dateFormat'                 => 'Y-m-d H:i:s',
                            'allowInlineLineBreaks'      => true,
                            'ignoreEmptyContextAndExtra' => true,
                        ],
                    ],
                ],
                // 文件日志配置
                [
                    // 日志 Handler 类名
                    'class'     => \Monolog\Handler\RotatingFileHandler::class,
                    // 日志 Handler 构造方法参数，具体参数详见该类构造方法
                    'construct' => [
                        'filename' => dirname(__DIR__) . '/logs/log.log',
                        'level'  => \Imi\Log\MonoLogger::DEBUG, // 开发调试环境
                        // 'level'  => \Imi\Log\MonoLogger::INFO,  // 生产环境
                        'bubble'   => true,
                    ],
                    // Formatter
                    'formatter' => [
                        // Formatter 类名
                        'class'     => \Monolog\Formatter\LineFormatter::class,
                        // Formatter 构造方法参数
                        'construct' => [
                            'dateFormat'                 => 'Y-m-d H:i:s',
                            'allowInlineLineBreaks'      => true,
                            'ignoreEmptyContextAndExtra' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'logger' => '设置 Logger 类，需继承 \Imi\Log\MonoLogger，一般无需设置',
],
```

> 结合上面的配置示例，可以支持所有 Monolog 的 Handler 和 Formatter，你也可以编写自定义的类。

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

直接记录异常日志：

```php
try {
    throw new \RuntimeException('test');
} catch(\Throwable $th) {
    App::getBean('ErrorLog')->onException($th);
}
```
