# InfluxDB

[toc]

InfluxDB 是一个开源的时间序列数据库，没有外部依赖性。它对记录指标、事件和执行分析很有用。

项目地址：<https://github.com/influxdata/influxdb>

imi-influxdb：<https://github.com/imiphp/imi-influxdb>

> 目前 imi 仅支持 InfluxDB < 1.8

## 安装

`composer require imiphp/imi-influxdb:~2.1.0`

## 使用说明

### InfluxDB 连接管理

#### 配置连接

`config.php`：

```php
[
    'influxDB'  => [
        'clients'   => [
            // default 是连接名称，可以随意更改
            'default'   => [
                'host'              => '127.0.0.1', // 主机名
                'port'              => 8086, // 端口
                'username'          => '', // 用户名
                'password'          => '', // 密码
                'defaultDatabase'   => '', // 默认数据库名
                'ssl'               => false, // 是否启用 SSL
                'verifySSL'         => false, // 是否验证 SSL 证书
                'timeout'           => 0, // 超时时间
                'connectTimeout'    => 0, // 连接超时时间
                'path'              => '/', // 请求路径前缀
                'createDatabase'    => true, // 当数据库不存在时，自动创建数据库
            ],
        ],
        'default'   => 'default', // 默认连接名
    ],
]
```

**TDengine InfluxDB 所需修改的配置：**

```php
// 连接配置一定要设置这几项
[
    'port'              => 6041,
    'path'              => '/influxdb/v1/',
    'createDatabase'    => false,
    'username'          => 'root',
    'password'          => 'taosdata',
]
```

#### 获取客户端对象

```php
use Imi\InfluxDB\InfluxDB;

$client = InfluxDB::getClient(); // 获取默认客户端
$client = InfluxDB::getClient('default'); // 获取指定名称客户端
```

#### 获取数据库对象

```php
use Imi\InfluxDB\InfluxDB;

$db = InfluxDB::getDatabase(); // 获取默认数据库名的对象
$db = InfluxDB::getDatabase('dbname'); // 获取指定数据库名的对象
$db = InfluxDB::getDatabase(null, 'default'); // 指定客户端名称
```

#### 使用数据库对象

```php
$db = InfluxDB::getDatabase();
$db->query(); // SQL 查询
$db->writePoints(); // 写入数据
```

> 详细用法请参考：<https://github.com/influxdata/influxdb-php>

#### InfluxDB ORM

详细用法请参考：<https://github.com/Yurunsoft/influxdb-orm>
