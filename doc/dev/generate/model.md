# 模型生成

[toc]

生成数据库中所有表的模型文件，支持 MySQL、PgSQL 虚拟列。

## 生成 MySQL 表模型

必选参数：
`namespace` 生成的Model所在命名空间

可选参数：
`--database` 数据库名，不传则取连接池默认配置的库名
`--baseClass` 生成时所继承的基类（自行实现务必继承框架的模型类），默认`Imi\Model\Model`，可选
`--poolName` 连接池名称，不传则取默认连接池
`--prefix` 传值则去除该表前缀
`--include` 要包含的表名，以半角逗号分隔
`--exclude` 要排除的表名，以半角逗号分隔
`--override` 是否覆盖已存在的文件，请慎重！`true`-全覆盖;`false`-不覆盖;`base`-覆盖基类;`model`-覆盖模型类;默认缺省状态为`false`
`--config` 配置文件。`true`-项目配置；`false`-忽略配置；php配置文件名-使用该配置文件。默认为`true`
`--basePath` 指定命名空间对应的基准路径，可选
`--entity` 序列化时是否使用驼峰命名(`true` or `false`),默认`true`,可选
`--sqlSingleLine` 生成的SQL为单行,默认`false`,可选
`--lengthCheck` 是否检查字符串字段长度,可选
`--ddlEncode` DDL 编码函数,可选
`--ddlDecode` DDL 解码函数,可选
`--bean` 模型对象是否作为 bean 类使用,默认 `true`,可选
`--incrUpdate` 模型是否启用增量更新，默认`false`，可选

示例：

```shell
vendor/bin/imi-xxx generate/model "命名空间"
```

> (`xxx` 根据运行模式不同而不同)

### 相关配置-MySQL

默认情况下，生成模型工具是无需任何配置的。

一般在大型项目中，我们会对项目进行模块划分。

每个模块有自己的模型、控制器等等。

这时候，模型文件必然不能生成在同一个命名空间下。

imi 支持两种自定义模型生成目标的配置方式。

#### 按命名空间指定-MySQL

项目配置文件：

```php
[
    'tools'  =>  [
        'generate/model'    =>  [
            'namespace' =>  [
                '命名空间1' =>  [
                    // 在该命名空间下，允许生成的表
                    'tables'    =>  [
                        '表1', // 用法1
                        // 用法2，支持指定参数
                        '表2' => [
                            // 下面所有字段都是非必须的，需要改哪个写哪个
                            'withRecords' => false, // 备份表记录
                            'fields' => [
                                '字段名' => [
                                    'typeDefinition' => false,
                                ],
                            ],
                            'bean' => true, // 模型对象是否作为 bean 类使用
                            'incrUpdate' => false, // 模型是否启用增量更新
                        ],
                    ],
                    // 备份哪些表的记录，不建议所有表都备份数据，一般用于字典表、默认数据等情况，即将废弃，推荐在 tables 中配置
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

#### 按表指定-MySQL

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
                    'fields' => [
                        '字段名' => [
                            'typeDefinition' => false,
                        ],
                    ],
                ],
                '表名2'   =>  [
                    'namespace' =>  '生成到的命名空间',
                ],
            ],
        ],
    ],
]
```

> 即将废弃，推荐使用 `按命名空间指定`

### 生成模型事件-MySQL

在生成模型时，会触发以下事件：

- `Imi\Model\Cli\Model\Event\Param\BeforeGenerateModels` - 生成所有模型前置事件
- `Imi\Model\Cli\Model\Event\Param\AfterGenerateModels` - 生成所有模型前置事件
- `Imi\Model\Cli\Model\Event\Param\BeforeGenerateModel` - 生成模型前置事件
- `Imi\Model\Cli\Model\Event\Param\AfterGenerateModel` - 生成模型前置事件

> 模型事件参数类都是事件名本身

`BeforeGenerateModel`、`AfterGenerateModel` 事件参数：

```php
/**
 * 命名空间.
 *
 * 例：app\Model
 */
public string $namespace = '';

/**
 * 基类名称.
 *
 * 例：app\Model\Base\ArticleBase
 */
public string $baseClassName = '';

/**
 * 类名.
 *
 * 例：Article
 */
public string $className = '';

/**
 * 完整类名.
 *
 * 包含命名空间
 *
 * 例：app\Model\Article
 */
public string $fullClassName = '';

/**
 * 表名.
 *
 * 例：article
 */
public string $tableName = '';

/**
 * Table 注解相关参数.
 *
 * [
 *     'name' => '表名',
 *     'id' => ['id'], // 主键列表
 *     'usePrefix' => false, // 是否使用表前缀
 * ].
 */
public array $table = [];

/**
 * 字段列表.
 *
 * [
 *     [
 *         'name'              => '字段名',
 *         'varName'           => '字段变量名',
 *         'type'              => '字段类型',
 *         'phpType'           => 'PHP注释字段类型',
 *         'phpDefinitionType' => 'PHP声明字段类型',
 *         'typeConvert'       => false, // 是否需要类型转换
 *         'length'            => 0, // 长度
 *         'accuracy'          => $accuracy, // 精度
 *         'nullable'          => true, // 是否允许为null
 *         'default'           => '默认值',
 *         'defaultValue'      => mixed, // PHP默认值
 *         'isPrimaryKey'      => false, // 是否为主键
 *         'primaryKeyIndex'   => -1, // 主键索引，-1为非主键
 *         'isAutoIncrement'   => false, // 是否自增
 *         'comment'           => '字段注释',
 *         'typeDefinition'    => false, // 是否声明PHP类型
 *         'ref'               => false, // 是否引用返回
 *         'unsigned'          => false, // 是否无符号
 *         'virtual'           => false, // 是否虚拟字段
 *     ]
 * ]
 */
public array $fields = [];

/**
 * 是否驼峰命名.
 */
public bool $camel = false;

/**
 * 是否使用 Bean 特性.
 */
public bool $bean = false;

/**
 * 是否增量更新.
 */
public bool $incrUpdate = false;

/**
 * 连接池名称.
 */
public ?string $poolName = null;

/**
 * 建表语句.
 *
 * 可能非 SQL 原文
 */
public string $ddl = '';

/**
 * 原始建表语句.
 */
public string $rawDDL = '';

/**
 * 建表语句解析回调.
 *
 * @var callable|null
 */
public $ddlDecode = null;

/**
 * 表注释.
 */
public string $tableComment = '';

/**
 * 是否检查字段长度.
 */
public bool $lengthCheck = false;
```

## 生成 PostgreSQL 表模型

必选参数：
`namespace` 生成的Model所在命名空间

可选参数：
`--database` 数据库名，不传则取连接池默认配置的库名
`--baseClass` 生成时所继承的基类（自行实现务必继承框架的模型类），默认`Imi\Model\Model`，可选
`--poolName` 连接池名称，不传则取默认连接池
`--prefix` 传值则去除该表前缀
`--include` 要包含的表名，以半角逗号分隔
`--exclude` 要排除的表名，以半角逗号分隔
`--override` 是否覆盖已存在的文件，请慎重！`true`-全覆盖;`false`-不覆盖;`base`-覆盖基类;`model`-覆盖模型类;默认缺省状态为`false`
`--config` 配置文件。`true`-项目配置；`false`-忽略配置；php配置文件名-使用该配置文件。默认为`true`
`--basePath` 指定命名空间对应的基准路径，可选
`--entity` 序列化时是否使用驼峰命名(`true` or `false`),默认`true`,可选
`--lengthCheck` 是否检查字符串字段长度,可选

示例：

```shell
vendor/bin/imi-cli generate/pgModel "命名空间"
```

### 相关配置-PostgreSQL

默认情况下，生成模型工具是无需任何配置的。

一般在大型项目中，我们会对项目进行模块划分。

每个模块有自己的模型、控制器等等。

这时候，模型文件必然不能生成在同一个命名空间下。

imi 支持两种自定义模型生成目标的配置方式。

#### 按表指定-PostgreSQL

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
                    'fields' => [
                        '字段名' => [
                            'typeDefinition' => false,
                        ],
                    ],
                ],
                '表名2'   =>  [
                    'namespace' =>  '生成到的命名空间',
                ],
            ],
        ],
    ],
]
```

#### 按命名空间指定-PostgreSQL

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
