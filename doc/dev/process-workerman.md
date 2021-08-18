# 进程工具

## 启动进程

开启一个进程，可以任意添加参数

必选参数：
`name` 进程名称，通过`@Process`注解定义

示例：

```shell
HttpDemo/bin/imi-cli process/start 进程名称

# 跟上进程需要获取的参数
HttpDemo/bin/imi-cli process/start 进程名称 --a 1 --b 2
```
