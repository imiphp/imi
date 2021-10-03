# 服务器配置

**imi 项目配置：**

```php
[
    // 服务器配置，写死
    'roadRunnerServer' => [
        'main' => [],
    ],

    // 日志配置
    'logger' => [
        'channels' => [
            'imi' => [
                'handlers' => [
                    // 命令行下的日志
                    [
                        'env'       => ['cli'],
                        'class'     => \Imi\Log\Handler\ConsoleHandler::class,
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
                    // RoadRunner worker 下日志
                    [
                        'env'       => ['roadrunner'],
                        'class'     => \Monolog\Handler\StreamHandler::class,
                        'construct' => [
                            'stream'  => 'php://stderr',
                        ],
                        'formatter' => [
                            'class'     => \Monolog\Formatter\LineFormatter::class,
                            'construct' => [
                                'format'                     => null,
                                'dateFormat'                 => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks'      => true,
                                'ignoreEmptyContextAndExtra' => true,
                            ],
                        ],
                    ],
                    // 文件日志
                    [
                        'class'     => \Monolog\Handler\RotatingFileHandler::class,
                        'construct' => [
                            'filename' => dirname(__DIR__) . '/logs/log.log', // 路径可以自定义
                        ],
                        'formatter' => [
                            'class'     => \Monolog\Formatter\LineFormatter::class,
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
    ],
    'beans' => [
        // 热更新配置
        'hotUpdate'	=>	[
            // 'status'	=>	false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

            // --- 文件修改时间监控 ---
            // 'monitorClass'	=>	\Imi\HotUpdate\Monitor\FileMTime::class,
            // 'timespan'	=>	1, // 检测时间间隔，单位：秒

            // --- Inotify 扩展监控 ---
            // 'monitorClass'	=>	\Imi\HotUpdate\Monitor\Inotify::class,
            // 'timespan'	=>	0, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

            // 'includePaths'	=>	[], // 要包含的路径数组
            // 'excludePaths'	=>	[], // 要排除的路径数组，支持通配符*
            // 'defaultPath'	=>	[], // 设为数组则覆盖默认的监控路径
        ],
    ],
]
```

**.rr.yaml 配置：**

```yaml
server:
  command: "php bin/bootstrap.php" # 项目 Worker 启动文件

http:
  # 配置监听地址
  address: 0.0.0.0:8080
  pool:
    num_workers: 2 # 设置进程数量
  # 静态文件访问配置
  static:
    dir: "."
    forbid: [""]
    allow: [".txt", ".php"]
    calculate_etag: false
    weak: false
    request:
      input: "custom-header"
    response:
      output: "output-header"

rpc:
  listen: tcp://127.0.0.1:6001
```

> 具体请查阅 RoadRunner 官方文档：<https://roadrunner.dev/docs>
