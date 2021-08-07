# imi-snowflake

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-snowflake.svg)](https://packagist.org/packages/imiphp/imi-snowflake)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.7.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-snowflake.svg)](https://github.com/imiphp/imi-snowflake/blob/master/LICENSE)

## 介绍

imi 框架的雪花算法生成组件

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-snowflake": "2.0.x-dev"
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
                // 'datacenterId'   => null, // 数据中心ID，未空时为0
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

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

`imi-snowflake` 遵循 MIT 开源协议发布，并提供免费使用。

## 鸣谢

感谢 [godruoyi/php-snowflake](https://github.com/godruoyi/php-snowflake) 为 imi-snowflake 提供算法驱动！

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
