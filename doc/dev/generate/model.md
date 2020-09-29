# 模型生成

生成数据库中所有表的模型文件，如果设置了`include`或`exclude`，则按照相应规则过滤表。

必选参数：
`-namespace` 生成的Model所在命名空间

可选参数：
`-database` 数据库名，不传则取连接池默认配置的库名
`-poolName` 连接池名称，不传则取默认连接池
`-prefix` 传值则去除该表前缀
`-include` 要包含的表名，以半角逗号分隔
`-exclude` 要排除的表名，以半角逗号分隔
`-override` 是否覆盖已存在的文件，请慎重！`true`-全覆盖;`false`-不覆盖;`base`-覆盖基类;`model`-覆盖模型类;默认缺省状态为`false`
`-config` 配置文件。`true`-项目配置；`false`-忽略配置；php配置文件名-使用该配置文件。默认为`true`
`-basePath` 指定命名空间对应的基准路径，可选
`-entity` 序列化时是否使用驼峰命名(`true` or `false`),默认`true`,可选
`-sqlSingleLine` 生成的SQL为单行,默认`false`,可选

示例：

```shell
HttpDemo/bin/imi generate/model -namespace "命名空间"
```

## 相关配置

默认情况下，生成模型工具是无需任何配置的。

一般在大型项目中，我们会对项目进行模块划分。

每个模块有自己的模型、控制器等等。

这时候，模型文件必然不能生成在同一个命名空间下。

imi 支持两种自定义模型生成目标的配置方式。

### 按表指定

项目配置文件：

```php
[
    'tools'  =>  [
        'generate/model'    =>  [
            'relation'  =>  [
                '表名1'   =>  [
                    'namespace' =>  '生成到的命名空间',
                    // 是否备份记录，不建议所有表都备份数据，一般用于字典表、默认数据等情况
                    'withRecords' => true,
                ],
                '表名2'   =>  [
                    'namespace' =>  '生成到的命名空间',
                ],
            ],
        ],
    ],
]
```

### 按命名空间指定

项目配置文件：

```php
[
    'tools'  =>  [
        'generate/model'    =>  [
            'namespace' =>  [
                '命名空间1' =>  [
                    // 在该命名空间下，允许生成的表
                    'tables'    =>  [
                        '表1',
                        '表2',
                    ],
                    // 备份哪些表的记录，不建议所有表都备份数据，一般用于字典表、默认数据等情况
                    'withRecords'   =>  [
                        '表1',
                    ],
                ],
                '命名空间2' =>  [
                    'tables'    =>  [
                        '表3',
                        '表4',
                    ],
                ],
            ]
        ],
    ],
]
```
