# imi-shared-memory

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-shared-memory.svg)](https://packagist.org/packages/imiphp/imi-shared-memory)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-shared-memory.svg)](https://github.com/imiphp/imi-shared-memory/blob/master/LICENSE)

## 介绍

基于 [Swoole Shared Memory](https://github.com/Yurunsoft/swoole-shared-memory) 开发的 `imi` 框架跨进程变量共享组件。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-shared-memory": "~1.0"
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

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

`imi-shared-memory` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://raw.githubusercontent.com/imiphp/imi-shared-memory/dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
