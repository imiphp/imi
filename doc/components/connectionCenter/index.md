# 连接中心

[TOC]

imi v3.0 版本引入了一个名为连接中心（ConnectionCenter）的全新组件。

这个全新的连接中心将代替旧版的连接池，成为一个中央管理各种数据库和客户端的解决方案。

连接中心还具备兼容性，可以同时与 Swoole、Workerman 和 PHP-FPM 等多种容器进行协同工作。

## 安装

`composer require imiphp/imi-connection-center:~3.0.0`

## 配置

项目的 `config/config.php`

```php
[
    'connectionCenter' => [
        // 连接管理器别名，自定义
        'mysql' => [
            // 连接管理器类名，详见下方《连接管理器》章节
            'manager' => \Imi\ConnectionCenter\Handler\Pool\PoolConnectionManager::class,
            // 连接管理器配置
            'config' => [
                // 连接驱动类名
                'driver' => \Imi\Db\MySQL\MySQLConnectionDriver::class,
                // 负载均衡器，默认不指定时使用随机负载均衡器，详见下方《负载均衡器》章节
                'loadBalancer' => \Imi\ConnectionCenter\LoadBalancer\RandomLoadBalancer::class,
                // 连接配置
                'resources' => [
                    // 格式一：数组
                    [
                        'host'        => env('MYSQL_SERVER_HOST', '127.0.0.1'),
                        'port'        => env('MYSQL_SERVER_PORT', 3306),
                        'username'    => env('MYSQL_SERVER_USERNAME', 'root'),
                        'password'    => env('MYSQL_SERVER_PASSWORD', 'root'),
                        'database'    => 'db_imi_test',
                        'charset'     => 'utf8mb4',
                        'weight'      => 1, // 权重，用于权重负载均衡器
                    ],
                    // 格式二：uri
                    // 协议://主机名[:端口号][/?key1=value1&key2=value2]
                    'tcp://127.0.0.1:3306/?username=root&password=root&database=db_test&charset=utf8mb4&weight=1',
                ],
                // 是否启用统计，启用后可能会有微量性能损耗
                'enableStatistics' => false,
                // 当前请求上下文资源检查状态间隔，单位：支持小数的秒。为 null/0 则每次都检查
                'requestResourceCheckInterval' => null,
                // 是否在获取资源时检查状态
                'checkStateWhenGetResource' => false,
            ],
        ],
    ],
]
```

> 仅为格式示例，具体用哪种客户端就参考对应的连接配置说明

## 连接管理器

### 连接池

**类名：** `Imi\ConnectionCenter\Handler\Pool\PoolConnectionManager`

得益于 Swoole 的常驻内存和协程特性，使用连接池可以减少频繁创建新连接，限制连接数量，提高响应速度和稳定性。

**使用场景：**

* Swoole 环境

**连接池配置：**

```php
[
    'connectionCenter' => [
        // 连接管理器别名，自定义
        'mysql' => [
            // 连接管理器类名，详见下方《连接管理器》章节
            'manager' => \Imi\ConnectionCenter\Handler\Pool\PoolConnectionManager::class,
            // 连接管理器配置
            'config' => [
                // 连接驱动类名
                'driver' => \Imi\Db\MySQL\MySQLConnectionDriver::class,
                // 负载均衡器，默认不指定时使用随机负载均衡器，详见下方《负载均衡器》章节
                'loadBalancer' => \Imi\ConnectionCenter\LoadBalancer\RandomLoadBalancer::class,
                // 连接配置
                'resources' => [
                    // 格式一：数组
                    [
                        'host'        => env('MYSQL_SERVER_HOST', '127.0.0.1'),
                        'port'        => env('MYSQL_SERVER_PORT', 3306),
                        'username'    => env('MYSQL_SERVER_USERNAME', 'root'),
                        'password'    => env('MYSQL_SERVER_PASSWORD', 'root'),
                        'database'    => 'db_imi_test',
                        'charset'     => 'utf8mb4',
                        'weight'      => 1, // 权重，用于权重负载均衡器
                    ],
                    // 格式二：uri
                    // 协议://主机名[:端口号][/?key1=value1&key2=value2]
                    'tcp://127.0.0.1:3306/?username=root&password=root&database=db_test&charset=utf8mb4&weight=1',
                ],
                // 是否启用统计，启用后可能会有微量性能损耗
                'enableStatistics' => false,
                // 当前请求上下文资源检查状态间隔，单位：支持小数的秒。为 null/0 则每次都检查
                'requestResourceCheckInterval' => null,
                // 是否在获取资源时检查状态
                'checkStateWhenGetResource' => false,
                // 连接池配置
                'pool' => [
                    // 最多资源数
                    'maxResources' => 32,
                    // 最少资源数，启动连接池时会自动填充到该数量的连接
                    'minResources' => 1,
                    // 资源回收时间间隔，单位：秒
                    'gcInterval' => 60,
                    // 获取资源最大存活时间，单位：秒；为 null 则不限制
                    'maxActiveTime' => null,
                    // 等待资源最大超时时间，单位：秒
                    'waitTimeout' => 3,
                    // 心跳时间间隔，单位：秒
                    'heartbeatInterval' => 60,
                    // 每次获取资源最长使用时间，单位：秒；为 null 则不限制
                    'maxUsedTime' => null,
                    // 资源创建后最大空闲回收时间，单位：秒；为 null 则不限制
                    'maxIdleTime' => null,
                ],
            ],
        ],
    ],
]
```

### 总是创建新连接

**类名：** `Imi\ConnectionCenter\Handler\AlwaysNew\AlwaysNewConnectionManager`

每次从连接管理器中获取连接时，都是一个新的连接，并且连接默认与连接管理器分离。

**使用场景：**

* HTTP 客户端
* 其它短连接客户端

### 全局单例

**类名：** `Imi\ConnectionCenter\Handler\Singleton\SingletonConnectionManager`

取出的连接在当前进程中唯一。

**使用场景：**

* PHP-FPM
* Workerman
* Swoole 下实现了多路复用的客户端

### 请求上下文单例

**类名：** `Imi\ConnectionCenter\Handler\RequestContextSingleton\RequestContextSingletonConnectionManager`

取出的连接在当前上下文中唯一，当前上下文销毁时，连接会被自动回收。

**使用场景：**

* HTTP 客户端
* 其它短连接客户端

## 使用

**门面类：** `Imi\ConnectionCenter\Facade\ConnectionCenter`

### 负载均衡器

#### 随机

**类名：** `Imi\ConnectionCenter\LoadBalancer\RandomLoadBalancer`

#### 轮询

**类名：** `Imi\ConnectionCenter\LoadBalancer\RoundRobinLoadBalancer`

#### 权重

**类名：** `Imi\ConnectionCenter\LoadBalancer\WeightLoadBalancer`

必须在 `resources` 中配置 `weight`！

### 获取连接

```php
$name = 'mysql'; // 连接管理器别名
$connection = ConnectionCenter::getConnection($name);
```

### 获取请求上下文连接

连接在请求上下文中唯一，多次调用不会每次从连接管理器中获取，请求上下文销毁时自动释放连接。

```php
$connection = ConnectionCenter::getRequestContextConnection('mysql');
```

### 连接的使用

```php
// 获取连接对象，如 PDO、mysqli 等
$instance = $connection->getInstance();

// 释放连接，回归连接管理器。
// 一般也可以不用手动释放，当 $connection 没有引用时，自动释放
$connection->release();

// 与连接管理器分离，作为一个独立连接
$connection->detach();

// 获取连接状态，详见常量：Imi\ConnectionCenter\Enum\ConnectionStatus
$connection->getStatus();
```
