# 雪花算法发号器

[toc]

## 介绍

imi 框架的雪花算法生成组件

Github: <https://github.com/imiphp/imi-snowflake>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-snowflake": "~2.1.0"
    }
}
```

然后执行 `composer update` 安装。

## 使用

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'snowflake'    =>  'Imi\Snowflake',
    ],
]
```

### 配置

配置 `@app.beans`：

```php
[
    'Snowflake'   =>  [
        'list'  =>  [
            // 可定义多个配置名称
            'testBasic' =>  [
                // 'datacenterId'   => null, // 数据中心ID，为空时为0
                // 'workerId'       => null, // 工作进程ID，为空时取当前进程ID
                // 'startTimeStamp' => null, // 开始时间戳，单位：毫秒
                // 'redisPool'      => null, // Redis 连接池名称，为空取默认连接池
            ],
        ],
    ],
]
```

### 生成ID

```php
$id = \Imi\Snowflake\SnowflakeUtil::id('testBasic');
```

### 解析ID

```php
$array = \Imi\Snowflake\SnowflakeUtil::parseId('testBasic', $id);
var_dump($array);
```

处理结果格式：

```php
array(4) {
  ["timestamp"]=>
  string(35) "10100100111111101010001000001110010"
  ["sequence"]=>
  string(12) "000000000000"
  ["workerid"]=>
  string(5) "00000"
  ["datacenter"]=>
  string(5) "00000"
}
```
