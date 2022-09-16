# 服务注册

[toc]

**服务注册**可以把当前服务注册到注册中心，便于其他服务使用**服务发现**、**负载均衡**来获取到某个节点，并与服务进行通信。

**支持的注册中心：**

* [x] Nacos ([imi-nacos](https://github.com/imiphp/imi-nacos))

* [ ] Consul

* [ ] Zookeeper ([imi-zookeeper](https://github.com/imiphp/imi-zookeeper))

……

**支持的容器：**

* Swoole

* Workerman

## 使用

### Nacos

**安装：**`composer require imiphp/imi-nacos:~2.1.0 imiphp/imi-service:~2.1.0`

**配置：**

`@app.beans`：

```php
[
    'ServiceRegistry' => [
        'drivers' => [
            [
                'driver' => \Imi\Nacos\Service\NacosServiceRegistry::class, // 驱动类名
                // 注册的服务列表
                'services' => [
                    'main', // 格式1：主服务器是 main，子服务器就是子服务器名
                    // 格式2：数组配置
                    [
                        // 所有参数按需设置
                        'server'     => 'main', // 主服务器是 main，子服务器就是子服务器名
                        // 'instanceId' => '实例ID',
                        'serviceId'  => 'main_test',
                        'weight'     => 1, // 权重
                        'uri'        => 'http://127.0.0.1:8080', // uri
                        // 'host'       => '127.0.0.1',
                        // 'port'       => 8080,
                        'metadata'   => [
                            // 'group' => 'DEFAULT_GROUP', // 分组
                            // 'namespaceId' => '', // 命名空间
                            // 'metadata' => [], // metadata
                            // 'ephemeral' => true, // 是否为临时实例
                        ],
                        // 'interface'  => 'eth0', // 网卡 interface 名，自动获取当前网卡IP时有效
                    ],
                ],
                'client' => [
                    // 注册中心客户端连接配置，每个驱动不同
                    'host'                => '127.0.0.1', // 主机名
                    'port'                => 8848, // 端口号
                    'prefix'              => '/', // 前缀
                    'username'            => 'nacos', // 用户名
                    'password'            => 'nacos', // 密码
                    'timeout'             => 60000, // 网络请求超时时间，单位：毫秒
                    'ssl'                 => false, // 是否使用 ssl(https) 请求
                    'authorizationBearer' => false, // 是否使用请求头 Authorization: Bearer {accessToken} 方式传递 Token，旧版本 Nacos 需要设为 true
                ],
                'heartbeat' => 3, // 心跳时间，单位：秒
            ],
        ],
    ],
]
```
