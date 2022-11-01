# Swoole Compiler 代码加密

[toc]

Swoole Compiler 是识沃科技推出的 PHP 代码加密和客户端授权解决方案，通过业内先进的代码加密技术，包括流程混淆、花指令、变量混淆、函数名混淆、虚拟机保护技术、扁平化代码、SCCP 优化等，将 PHP 程序源代码编译为二进制指令，来保护您的源代码。

服务地址：<https://business.swoole.com/compiler.html>

## 使用说明

使用 `imi` + `Swoole Compiler` 你需要注意如下事项：

* 加密前：`vendor/bin/imi-swoole imi/buildRuntime`

* 加密后启动服务：`vendor/bin/imi-swoole swoole/start --app-runtime={你的项目路径}/.runtime/swoole/runtime/`
