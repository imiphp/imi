# 通过配置中心配置连接池

imi 支持在配置中心里配置连接池的信息。

这里以 Nacos、MySQL 为例，其它配置中心驱动或 Redis 连接池配置写法以此类推。

## 安装组件

参考 [配置中心](center.html) 中的相关说明。

## 创建配置

在配置中心系统里创建一个配置，我们这里命名为 `mysql.resource`，`Group` 设置为 `imi`，你也可以改为其它名字。

配置内容：（相关配置请修改为你自己的）

```js
{
    "host": "127.0.0.1",
    "port": 3306,
    "username": "root",
    "password": "root",
    "database": "mysql",
    "charset": "utf8mb4"
}
```

## 修改 imi 配置

配置好连接池，这里的 `resource` 配置是写的空数组，当然你也可以配置一个可以连接的资源配置。

**config.php：**

```php
// 连接池配置
'pools'    => [
    // 主数据库
    'maindb'    => [
        'pool'    => [
            'class'        => \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
            'config'       => [
                'maxResources'    => 10,
                'minResources'    => 1,
            ],
        ],
        'resource'    => [],
    ],
],
'db'    => [
    'defaultPool'   => 'maindb', // 默认连接池
],
```

**bean.php：**

```php
[
    'ConfigCenter' => [
        // 其它配置参考《配置中心》文档
        'configs' => [
            'nacos' => [
                // 其它配置参考《配置中心》文档
                // 配置项
                'configs' => [
                    // key 指定覆盖的配置键名
                    '@app.pools.maindb.resource' => [
                        'key'   => 'mysql.resource', // 第一步创建的配置名称
                        'group' => 'imi', // 分组名，仅 Nacos
                        'type'  => 'json', // 配置内容类型，我们使用的是 json
                    ],
                ],
            ],
        ],
    ],
]
```

## 启动服务

`vendor/bin/imi-swoole swoole/start`

完美生效！
