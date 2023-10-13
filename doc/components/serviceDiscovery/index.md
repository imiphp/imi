# 服务发现（负载均衡）

[toc]

**服务发现**就是从注册中心获取节点列表。

**负载均衡**就是从节点列表中，通过特定算法，获取本次通信使用的节点。

**支持的注册中心：**

* [x] Nacos ([imi-nacos](https://github.com/imiphp/imi-nacos))

* [ ] Consul

* [ ] Zookeeper ([imi-zookeeper](https://github.com/imiphp/imi-zookeeper))

……

**支持的容器：**

* Swoole

* Workerman

## 使用

### 配置

#### Nacos

**安装：**`composer require imiphp/imi-nacos:~3.0.0 imiphp/imi-service:~3.0.0`

**配置：**

`@app.beans`：

```php
[
    'ServiceDiscovery' => [
        'drivers' => [
            [
                'driver'       => \Imi\Nacos\Service\NacosServiceDiscoveryDriver::class, // 服务发现驱动
                // 'client' => \Imi\Service\Discovery\DiscoveryClient::class, // 服务发现客户端，如无必要不需要修改
                // 负载均衡配置
                'loadBalancer' => \Imi\Service\LoadBalancer\RandomLoadBalancer::class, // 负载均衡-随机
                // 'loadBalancer' => \Imi\Service\LoadBalancer\RoundRobinLoadBalancer::class, // 负载均衡-轮询
                // 'loadBalancer' => \Imi\Service\LoadBalancer\WeightLoadBalancer::class, // 负载均衡-权重
                // 发现服务列表
                'services' => [
                    'serviceName', // 改为你的服务名称
                ],
                'client' => [
                    // Nacos 客户端连接配置
                    'host'                => '127.0.0.1', // 主机名
                    'port'                => 8848, // 端口号
                    'prefix'              => '/', // 前缀
                    'username'            => 'nacos', // 用户名
                    'password'            => 'nacos', // 密码
                    'timeout'             => 60000, // 网络请求超时时间，单位：毫秒
                    'ssl'                 => false, // 是否使用 ssl(https) 请求
                    'authorizationBearer' => false, // 是否使用请求头 Authorization: Bearer {accessToken} 方式传递 Token，旧版本 Nacos 需要设为 true
                ],
                'cacheTTL' => 60, // 缓存时间，单位：秒。默认为60秒，设为0不启用缓存
            ],
        ],
    ],
]
```

### 获取服务可用节点

```php
/** @var \Imi\Service\Discovery\ServiceDiscovery $serviceDiscovery */
$serviceDiscovery = \Imi\App::getBean('ServiceDiscovery');
$service = $serviceDiscovery->getInstance('服务名称');

$service->getInstanceId(); // 实例ID，string
$service->getServiceId(); // 服务ID，string
$service->getWeight(); // 权重，float
$service->getUri(); // \Imi\Util\Uri
$service->getMetadata(); // 元数据，数组

// 获取服务实例的ip及端口的常用写法
$uri = $service->getUri();
$host = $uri->getHost();
$port = $uri->getPort();
```
