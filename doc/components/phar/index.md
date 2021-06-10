# Phar 支持

目前 `imi` 已经完全支持了 Phar 模式运行。（其实可以不看，直接用）

`Phar` 模式生成 `phar` 文件需要修改`php.ini`，加入以下配置：

```ini
phar.readonly=Off
```

`imi` 内置打包生成 phar 命令：`bin/build-phar`

目前，默认（强制）生成为`imi/src/imi.phar`