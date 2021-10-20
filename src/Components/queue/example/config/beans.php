<?php

declare(strict_types=1);

$rootPath = \dirname(__DIR__) . '/';

return [
    'hotUpdate'    => [
        // 'status'    =>    false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

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
    'AutoRunProcessManager' => [
        'processes' => [
            // 加入队列消费进程，非必须，你也可以自己写进程消费
            'QueueConsumer',
        ],
    ],
    'imiQueue'  => [
        // 默认队列
        'default'   => 'test1',
        // 队列列表
        'list'  => [
            // 队列名称
            'test1' => [
                // 使用的队列驱动
                'driver'        => 'RedisQueueDriver',
                // 消费协程数量
                'co'            => 1,
                // 消费进程数量；可能会受进程分组影响，以同一组中配置的最多进程数量为准
                'process'       => 1,
                // 消费循环尝试 pop 的时间间隔，单位：秒
                'timespan'      => 0.1,
                // 进程分组名称
                'processGroup'  => 'a',
                // 自动消费
                'autoConsumer'  => true,
                // 消费者类
                'consumer'      => 'AConsumer',
                // 驱动类所需要的参数数组
                'config'        => [
                    'poolName'  => 'redis',
                    'prefix'    => 'imi:queue:test:',
                ],
            ],
            'test2' => [
                'driver'        => 'RedisQueueDriver',
                'co'            => 1,
                'process'       => 1,
                'timespan'      => 0.1,
                'processGroup'  => 'b',
                'autoConsumer'  => true,
                'consumer'      => 'BConsumer',
                'config'        => [
                    'poolName'  => 'redis',
                    'prefix'    => 'imi:queue:test:',
                ],
            ],
        ],
    ],
];
