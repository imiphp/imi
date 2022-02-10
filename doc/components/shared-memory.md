# 跨进程变量共享

## 介绍

基于 [Swoole Shared Memory](https://github.com/Yurunsoft/swoole-shared-memory) 开发的 `imi` 框架跨进程变量共享组件。

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-shared-memory": "~2.1.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'SharedMemory'    =>  'Imi\SharedMemory',
    ],
    'pools'    =>    [
        // 客户端连接池
        'sharedMemory'  =>  [
            'async' =>  [
                'pool'    =>    [
                    'class'        =>    \Imi\SharedMemory\Pool\ClientPool::class,
                    'config'    =>    [
                        'maxResources'    =>    100,
                        'minResources'    =>    0,
                    ],
                ],
                'resource'  =>  [
                    // 以下为可选配置

                    // 存储器类型，注意和下面的类名不同
                    // 'storeTypes'    =>  [
                    //     \Yurun\Swoole\SharedMemory\Client\Store\KV::class,
                    //     \Yurun\Swoole\SharedMemory\Client\Store\Stack::class,
                    //     \Yurun\Swoole\SharedMemory\Client\Store\Queue::class,
                    //     \Yurun\Swoole\SharedMemory\Client\Store\PriorityQueue::class,
                    //      'name'  =>  'XXXClass',
                    // ],

                    // unix socket 文件名，默认会自动放 runtime 目录中
                    // 'socketFile'    =>  '',
                ],
            ],
        ]
    ],
    // 以下为可选配置
    // 'swooleSharedMemory'    =>  [
        // unix socket 文件名，默认会自动放 runtime 目录中
        // 'socketFile'    =>  '',

        // 存储器类型，注意和上面的类名不同
        // 'storeTypes'    =>  [
        //     \Yurun\Swoole\SharedMemory\Store\KV::class,
        //     \Yurun\Swoole\SharedMemory\Store\Stack::class,
        //     \Yurun\Swoole\SharedMemory\Store\Queue::class,
        //     \Yurun\Swoole\SharedMemory\Store\PriorityQueue::class,
        //      'name'  =>  'XXXClass',
        // ],

        // 默认连接池名
        // 'defaultPool'   =>  'sharedMemory'
    // ],
]
```

在代码中操作：

```php
// 方法一
SharedMemory::use('KV', function(\Yurun\Swoole\SharedMemory\Client\Store\KV $kv){
    $kv->set('a', 1);
});

// 方法二
$kv = SharedMemory::getInstance()->getObject('KV');
$kv->set('a', 1);
```
