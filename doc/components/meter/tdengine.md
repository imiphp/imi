# TDengine

[toc]

TDengine 是一款开源、高性能、云原生的时序数据库，且针对物联网、车联网、工业互联网、金融、IT 运维等场景进行了优化。TDengine 的代码，包括集群功能，都在 GNU AGPL v3.0 下开源。除核心的时序数据库功能外，TDengine 还提供缓存、数据订阅、流式计算等其它功能以降低系统复杂度及研发和运维成本。

项目地址：<https://github.com/taosdata/TDengine>

## 说明

imi 支持将服务指标监控的数据写入 TDengine。

**支持的协议：**

* [ ] TDengine Schemaless（开发中）
* [x] InfluxDB 兼容接口

更多写入协议支持开发中……

## 使用

### InfluxDB 兼容接口

这个写入方式主要依赖 imi-influxdb，文档请移步：<https://doc.imiphp.com/v2.1/components/meter/influxdb.html>

在 InfluxDB 配置基础上，只需要做如下修改，即可兼容 TDengine：

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
