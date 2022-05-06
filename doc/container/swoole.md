# Swoole

[toc]

imi v0.0.1 版本开始，支持运行在 Swoole 环境中。

组件引入：`composer require imiphp/imi-swoole`

## 核心特性

| 特性 | 是否支持 |
|-|-
| Http | ✔ |
| Http2 | ✔ |
| WebSocket | ✔ |
| TCP | ✔ |
| UDP | ✔ |
| MQTT | ✔ |

## 命令

启动服务: `vendor/bin/imi-swoole swoole/start`

停止服务: `vendor/bin/imi-swoole swoole/stop`

重载服务: `vendor/bin/imi-swoole swoole/reload`

> 重载服务仅干掉所有 Worker 进程，让他们重新启动，并不能代替冷重启，有些代码是无法靠重载更新的。
