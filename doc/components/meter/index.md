# 服务指标监控

[toc]

在微服务中，我们需要监控一些数据指标，来保证系统的正常运行和告警。

imi 接入了服务指标监控能力，提供了 [imi-meter](https://github.com/imiphp/imi-meter) 组件作为抽象。

一般都会结合 [Grafana](https://github.com/grafana/grafana) 实现图形化。

**支持的中间件：**

* [x] [Prometheus](/v2.1/components/meter/prometheus.html)

* [x] [InfluxDB](/v2.1/components/meter/influxdb.html)

* [x] [TDengine](/v2.1/components/meter/tdengine.html)

……
