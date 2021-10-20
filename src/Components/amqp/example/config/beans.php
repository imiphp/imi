<?php

declare(strict_types=1);

use Imi\Util\Imi;

$rootPath = \dirname(__DIR__) . '/';

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
    'AutoRunProcessManager' => [
        'processes' => Imi::checkAppType('swoole') ? [
            'TestProcess',
            'QueueConsumer',
        ] : [
            'TestProcess1',
            'TestProcess2',
            'QueueConsumer',
        ],
    ],
    'AMQP'  => [
        'defaultPoolName'   => 'rabbit',
    ],
    'imiQueue'  => [
        // 默认队列
        'default'   => 'QueueTest1',
        // 队列列表
        'list'  => [
            // 队列名称
            'QueueTest1' => [
                // 使用的队列驱动
                'driver'        => 'AMQPQueueDriver',
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
                    // AMQP 连接池名称
                    'poolName'      => 'rabbit',
                    // Redis 连接池名称
                    'redisPoolName' => 'redis',
                    // Redis 键名前缀
                    'redisPrefix'   => 'QueueTest1:',
                    // 可选配置：
                    // 支持消息删除功能，依赖 Redis
                    'supportDelete' => true,
                    // 支持消费超时队列功能，依赖 Redis，并且自动增加一个队列
                    'supportTimeout' => true,
                    // 支持消费失败队列功能，自动增加一个队列
                    'supportFail' => true,
                    // 循环尝试 pop 的时间间隔，单位：秒
                    'timespan'  => 0.03,
                    // 本地缓存的队列长度。由于 AMQP 不支持主动pop，而是主动推送，所以本地会有缓存队列，这个队列不宜过大。
                    'queueLength'   => 16,
                    // 消息类名
                    'message'   => \Imi\AMQP\Queue\JsonAMQPMessage::class,
                ],
            ],
        ],
    ],
];
