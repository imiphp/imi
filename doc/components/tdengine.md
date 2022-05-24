# TDengine

[TOC]

## TDengine 介绍

TDengine 是一款高性能、分布式、支持 SQL 的时序数据库（Time-Series Database）。而且除时序数据库功能外，它还提供缓存、数据订阅、流式计算等功能，最大程度减少研发和运维的复杂度，且核心代码，包括集群功能全部开源（开源协议，AGPL v3.0）。与其他时序数据数据库相比，TDengine 有以下特点：

- **高性能**：通过创新的存储引擎设计，无论是数据写入还是查询，TDengine 的性能比通用数据库快 10 倍以上，也远超其他时序数据库，而且存储空间也大为节省。

- **分布式**：通过原生分布式的设计，TDengine 提供了水平扩展的能力，只需要增加节点就能获得更强的数据处理能力，同时通过多副本机制保证了系统的高可用。

- **支持 SQL**：TDengine 采用 SQL 作为数据查询语言，减少学习和迁移成本，同时提供 SQL 扩展来处理时序数据特有的分析，而且支持方便灵活的 schemaless 数据写入。

- **All in One**：将数据库、消息队列、缓存、流式计算等功能融合一起，应用无需再集成 Kafka/Redis/HBase/Spark 等软件，大幅降低应用开发和维护成本。

- **零管理**：安装、集群几秒搞定，无任何依赖，不用分库分表，系统运行状态监测能与 Grafana 或其他运维工具无缝集成。

- **零学习成本**：采用 SQL 查询语言，支持 Python、Java、C/C++、Go、Rust、Node.js 等多种编程语言，与 MySQL 相似，零学习成本。

- **无缝集成**：不用一行代码，即可与 Telegraf、Grafana、EMQX、Prometheus、StatsD、collectd、Matlab、R 等第三方工具无缝集成。

- **互动 Console**: 通过命令行 console，不用编程，执行 SQL 语句就能做即席查询、各种数据库的操作、管理以及集群的维护.

TDengine 可以广泛应用于物联网、工业互联网、车联网、IT 运维、能源、金融等领域，让大量设备、数据采集器每天产生的高达 TB 甚至 PB 级的数据能得到高效实时的处理，对业务的运行状态进行实时的监测、预警，从大数据中挖掘出商业价值。

## imi-tdengine

封装 tdengine 连接池，支持在 imi 框架中使用。

本组件支持 RESTful 和扩展两种方式实现。

扩展安装请移步：<https://github.com/Yurunsoft/php-tdengine>

### 使用

引入本组件：`composer require imiphp/imi-tdengine`

#### 配置

项目配置文件：(config/config.php)

```php
// Swoole 连接池配置，非 Swoole 不要配
// 连接池必须使用扩展
'pools'    => [
    '连接池名'    => [
        'pool'    => [
            'class'        => \Imi\TDengine\Pool\TDengineExtensionCoroutinePool::class,
            'config'       => [
                'maxResources'    => 10,
                'minResources'    => 0,
            ],
        ],
        'resource'    => [
            'host'            => '127.0.0.1',
            'port'            => 6030,
            'user'            => 'root',
            'password'        => 'taosdata',
            'db'              => 'db_test',
        ],
    ],
],

'beans' => [
    // db 配置
    'TDengine' => [
        'defaultPoolName' => '默认连接名',
        'connections'     => [
            // 扩展配置，不需要可不配
            '连接名1' => [
                'extension'       => true, // 必须设为 true
                'host'            => '127.0.0.1',
                'port'            => 6030,
                'user'            => 'root',
                'password'        => 'taosdata',
                'db'              => 'db_test',
            ],
            // restful 配置，不需要可不配
            '连接名2' => [
                'host'            => '127.0.0.1',
                'hostName'        => '', // 域名，没有可不填
                'port'            => 6041,
                'user'            => 'root',
                'password'        => 'taosdata',
                'db'              => 'db_test'
                'ssl'             => false,
                'timestampFormat' => \Yurun\TDEngine\Constants\TimeStampFormat::LOCAL_STRING,
                'keepAlive'       => true,
            ],
        ],
    ],
],
```

#### 模型

使用参考：<https://github.com/Yurunsoft/tdengine-orm>

#### 连接操作

直接操作连接对象，执行 SQL 语句

##### 获取连接对象

```php
// 获取默认连接名的连接
$connection = \Imi\TDengine\Pool\TDengine::getConnection();
// 获取指定连接名的连接
$connection = \Imi\TDengine\Pool\TDengine::getConnection('连接名123');
// 如果是扩展，$connection 类型为 \TDengine\Connection
// 如果是 restful，$connection 类型为 \Yurun\TDEngine\Client
```

##### 扩展用法

**查询：**

```php
// 查询
$resource = $connection->query($sql); // 支持查询和插入
// 获取结果集时间戳字段的精度，0 代表毫秒，1 代表微秒，2 代表纳秒
$resource->getResultPrecision();
// 获取所有数据
$resource->fetch();
// 获取一行数据
$resource->fetchRow();
// 获取字段数组
$resource->fetchFields();
// 获取列数
$resource->getFieldCount();
// 获取影响行数
$resource->affectedRows();
// 获取 SQL 语句
$resource->getSql();
// 获取连接对象
$resource->getConnection();
// 关闭资源（一般不需要手动关闭，变量销毁时会自动释放）
$resource->close();
```

**参数绑定：**

```php
// 查询
$stmt = $connection->prepare($sql); // 支持查询和插入，参数用?占位
// 绑定参数方法1
$stmt->bindParams(
    // [字段类型, 值]
    [TDengine\TSDB_DATA_TYPE_TIMESTAMP, $time1],
    [TDengine\TSDB_DATA_TYPE_INT, 36],
    [TDengine\TSDB_DATA_TYPE_FLOAT, 44.5],
);
// 绑定参数方法2
$stmt->bindParams([
    // ['type' => 字段类型, 'value' => 值]
    ['type' => TDengine\TSDB_DATA_TYPE_TIMESTAMP, 'value' => $time2],
    ['type' => TDengine\TSDB_DATA_TYPE_INT, 'value' => 36],
    ['type' => TDengine\TSDB_DATA_TYPE_FLOAT, 'value' => 44.5],
]);
// 执行 SQL，返回 Resource，使用方法同 query() 返回值
$resource = $stmt->execute();
// 获取 SQL 语句
$stmt->getSql();
// 获取连接对象
$stmt->getConnection();
// 关闭（一般不需要手动关闭，变量销毁时会自动释放）
$stmt->close();
```

**字段类型：**

| 参数名称 | 说明 |
| ------------ | ------------ 
| `TDengine\TSDB_DATA_TYPE_NULL` | null |
| `TDengine\TSDB_DATA_TYPE_BOOL` | bool |
| `TDengine\TSDB_DATA_TYPE_TINYINT` | tinyint |
| `TDengine\TSDB_DATA_TYPE_SMALLINT` | smallint |
| `TDengine\TSDB_DATA_TYPE_INT` | int |
| `TDengine\TSDB_DATA_TYPE_BIGINT` | bigint |
| `TDengine\TSDB_DATA_TYPE_FLOAT` | float |
| `TDengine\TSDB_DATA_TYPE_DOUBLE` | double |
| `TDengine\TSDB_DATA_TYPE_BINARY` | binary |
| `TDengine\TSDB_DATA_TYPE_TIMESTAMP` | timestamp |
| `TDengine\TSDB_DATA_TYPE_NCHAR` | nchar |
| `TDengine\TSDB_DATA_TYPE_UTINYINT` | utinyint |
| `TDengine\TSDB_DATA_TYPE_USMALLINT` | usmallint |
| `TDengine\TSDB_DATA_TYPE_UINT` | uint |
| `TDengine\TSDB_DATA_TYPE_UBIGINT` | ubigint |

##### restful 用法

```php
// 通过 sql 方法执行 sql 语句
var_dump($connection->sql('create database if not exists db_test'));
var_dump($connection->sql('show databases'));
var_dump($connection->sql('create table if not exists db_test.tb (ts timestamp, temperature int, humidity float)'));
var_dump($connection->sql(sprintf('insert into db_test.tb values(%s,%s,%s)', time() * 1000, mt_rand(), mt_rand() / mt_rand())));

$result = $connection->sql('select * from db_test.tb');

$result->getResponse(); // 获取接口原始返回数据

// 获取列数据
foreach ($result->getColumns() as $column)
{
    $column->getName(); // 列名
    $column->getType(); // 列类型值
    $column->getTypeName(); // 列类型名称
    $column->getLength(); // 类型长度
}

// 获取数据
foreach ($result->getData() as $row)
{
    echo $row['列名']; // 经过处理，可以直接使用列名获取指定列数据
}

$result->getStatus(); // 告知操作结果是成功还是失败；同接口返回格式

$result->getHead(); // 表的定义，如果不返回结果集，则仅有一列“affected_rows”。（从 2.0.17 版本开始，建议不要依赖 head 返回值来判断数据列类型，而推荐使用 column_meta。在未来版本中，有可能会从返回值中去掉 head 这一项。）；同接口返回格式

$result->getRow(); // 表明总共多少行数据；同接口返回格式
```
