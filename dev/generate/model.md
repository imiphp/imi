生成Model

必选参数：
`-namespace` 生成的Model所在命名空间

可选参数：
`-database` 数据库名，不传则取连接池默认配置的库名
`-poolName` 连接池名称，不传则取默认连接池
`-prefix` 传值则去除该表前缀
`-include` 要包含的表名，以半角逗号分隔
`-exclude` 要排除的表名，以半角逗号分隔
`-override` 是否覆盖已存在的文件，请慎重！(`true`/`false`)

示例：

```
php tool.php generate/model namespace 命名空间
```