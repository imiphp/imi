<?php

use Imi\Server\Type;

return [
    // 项目根命名空间
    'namespace'    => 'ApiDocApp',

    // 配置文件
    'configs'    => [
        'beans'        => __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    => [
        'ApiDocApp\Listener',
        'ApiDocApp\Task',
    ],

    // 组件命名空间
    'components'    => [
        'ApiDoc'  => 'Imi\ApiDoc',
    ],

    // 主服务器配置
    'mainServer'    => [
        'namespace'     => 'ApiDocApp\ApiServer',
        'type'          => Type::HTTP,
        'host'          => '127.0.0.1',
        'port'          => 8080,
        'configs'       => [
            'worker_num'        => 1,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        => [
    ],

    // 连接池配置
    'pools'    => [
    ],
];
