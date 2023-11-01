# 服务器配置

[toc]

**imi 项目配置：**

```php
[
    // 服务器配置，写死
    'roadRunnerServer' => [
        'main' => [
            'namespace' => 'ImiApp\ApiServer', // Http 服务的命名空间，为空则使用项目命名空间
            // $request->getAppUri() 参数替换，每个参数都是可选项
            // 下面例子最终获取到的 Uri 为：https://root:123@imi-test:1234/test?id=666#test
            'appUri' => [
                'host'     => 'imi-test',   // 主机名
                'port'     => 1234,         // 端口
                'scheme'   => 'https',      // 协议
                'user'     => 'root',       // 用户名
                'pass'     => '123',        // 密码
                'path'     => '/test',      // 路径
                'query'    => 'id=666',     // 查询参数
                'fragment' => 'test',       // 锚点
            ],
            // 也支持回调
            'appUri' => function(\Imi\Util\Uri $uri) {
                return $uri->withHost('imi-test');
            },
        ],
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
        'hotUpdate'    =>    [
            // 'status'    =>    false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

            // --- 文件修改时间监控 ---
            // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\FileMTime::class,
            // 'timespan'    =>    1, // 检测时间间隔，单位：秒

            // --- Inotify 扩展监控 ---
            // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\Inotify::class,
            // 'timespan'    =>    0, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

            // 'includePaths'    =>    [], // 要包含的路径数组
            // 'excludePaths'    =>    [], // 要排除的路径数组，支持通配符*
            // 'defaultPath'    =>    [], // 设为数组则覆盖默认的监控路径
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
