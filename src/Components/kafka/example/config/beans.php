<?php

use Imi\Log\LogLevel;

$rootPath = dirname(__DIR__) . '/';

return [
    'hotUpdate'    => [
        'status'    => false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

        // --- 文件修改时间监控 ---
        // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\FileMTime::class,
        'timespan'    => 1, // 检测时间间隔，单位：秒

        // --- Inotify 扩展监控 ---
        // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\Inotify::class,
        // 'timespan'    =>    1, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

        // 'includePaths'    =>    [], // 要包含的路径数组
        'excludePaths'    => [
            $rootPath . '.git',
            $rootPath . 'bin',
            $rootPath . 'logs',
        ], // 要排除的路径数组，支持通配符*
    ],
    'Logger'    => [
        'exHandlers'    => [
            // info 级别日志不输出trace
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'        => [
                        LogLevel::INFO,
                        LogLevel::DEBUG,
                        LogLevel::NOTICE,
                    ],
                    'fileName'      => dirname(__DIR__) . '/.runtime/logs/{Y}-{m}-{d}.log',
                    'format'        => '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}',
                ],
            ],
            // 指定级别日志输出trace
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'        => [
                        LogLevel::ALERT,
                        LogLevel::CRITICAL,
                        LogLevel::EMERGENCY,
                        LogLevel::ERROR,
                        LogLevel::WARNING,
                    ],
                    'fileName'      => dirname(__DIR__) . '/.runtime/logs/{Y}-{m}-{d}.log',
                    'format'        => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}\n{trace}",
                    'traceFormat'   => '#{index}  {call} called at [{file}:{line}]',
                    'traceMinimum'  => true,
                ],
            ],
        ],
    ],
    'AutoRunProcessManager' => [
        'processes' => [
            'TestProcess',
            'QueueConsumer',
        ],
    ],
    'Kafka'  => [
        'defaultPoolName'   => 'kafka',
    ],
    'imiQueue'  => [
        // 默认队列
        'default'   => 'QueueTest1',
        // 队列列表
        'list'  => [
            // 队列名称
            'QueueTest1' => [
                // 使用的队列驱动
                'driver'        => 'KafkaQueueDriver',
                // 消费协程数量
                'co'            => 1,
                // 消费进程数量；可能会受进程分组影响，以同一组中配置的最多进程数量为准
                'process'       => 1,
                // 消费循环尝试 pop 的时间间隔，单位：秒（仅使用消费者类时有效）
                'timespan'      => 0.1,
                // 进程分组名称
                'processGroup'  => 'a',
                // 自动消费
                'autoConsumer'  => true,
                // 消费者类
                'consumer'      => 'QueueTestConsumer',
                // 驱动类所需要的参数数组
                'config'        => [
                    // Kafka 连接池名称
                    'poolName'      => 'kafka',
                ],
            ],
        ],
    ],
];
