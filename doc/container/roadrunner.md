# RoadRunner

RoadRunner 是一款开源（MIT 协议）高性能 PHP 应用服务器、负载均衡器和进程管理器。

它支持作为服务运行，能够在每个项目的基础上扩展功能。

RoadRunner 包括 PSR-7/PSR-17 兼容的 HTTP 和 HTTP/2 服务器，可用于取代传统的 Nginx+FPM 设置，具有更高的性能和灵活性。

RoadRunner Github：<https://github.com/spiral/roadrunner>

RoadRunner 官方文档：<https://roadrunner.dev/docs>

组件引入：`composer require imiphp/imi-roadrunner`

## 核心特性

| 特性 | 是否支持 |
|-|-
| Http | ✔ |
| Http2 | ✔ |
| WebSocket |  |
| TCP |  |
| UDP |  |
| MQTT |  |

## 命令

启动服务: `vendor/bin/imi-cli rr/start`

停止服务: `vendor/bin/imi-cli rr/stop`

重载服务: `vendor/bin/imi-cli rr/reload`

以上所有命令都支持以下参数：

```shell
  -w, --workDir[=WORKDIR]              工作路径
  -c, --config[=CONFIG]                配置文件路径，默认 .rr.yaml
```
