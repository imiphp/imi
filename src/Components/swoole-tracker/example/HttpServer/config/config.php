<?php

return [
    // 项目根命名空间
    'namespace'    => 'Imi\SwooleTracker\Example\HttpServer',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
    ],

    // 组件命名空间
    'components'    => [
        'SwooleTracker'       => 'Imi\SwooleTracker',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'    => 'Imi\SwooleTracker\Example\HttpServer\ApiServer',
        'type'         => Imi\Server\Type::HTTP,
        'host'         => '127.0.0.1',
        'port'         => 13000,
        'configs'      => [
            'worker_num'        => 1,
            'task_worker_num'   => 0,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
    ],
];
