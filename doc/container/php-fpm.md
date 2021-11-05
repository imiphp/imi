# PHP-FPM

imi v2.0 版本开始，支持运行在 PHP-FPM 环境中。

组件引入：`composer require imiphp/imi-fpm`

## 核心特性

| 特性 | 是否支持 |
|-|-
| Http | ✔ |
| Http2 |  |
| WebSocket |  |
| TCP |  |
| UDP |  |
| MQTT |  |

## 性能优化

### 移除不必要的组件

移除没有用到的组件，比如 Swool、Workerman 等

### 项目设为非 debug 模式

项目配置文件中，设置：

```php
[
    'debug' => false,
]
```

当然你也可以在项目 Main 文件中，设置 imi 为非 debug 模式：

```php
\Imi\App::setDebug(false);
```

### 生成运行时缓存

在生产环境中，我们建议你每次部署都重新生成运行时缓存，以获得性能提升。

生成项目缓存命令：`vendor/bin/imi-cli imi/buildRuntime --app-namespace "项目命名空间" --runtimeMode=fpm`

> 生成后，会在 `.runtime` 目录中生成 `runtime`，部署更新项目时需要手动删除

---

如果你是开发时，建议生成框架运行时缓存：`vendor/bin/imi-cli imi/buildImiRuntime --app-namespace "项目命名空间" --runtimeMode=fpm`

> 生成后，会在 `.runtime` 目录中生成 `imi-runtime`，更新框架后需要手动删除

### 配置优化

在项目配置文件中配置：

```php
return [
    'imi' => [
        'annotation' => [
            'cache' => true, // 启用注解解析文件缓存；修改代码不生效时，修改注解需要删除 `.runtime/annotation` 目录缓存
        ],
        'bean' => [
            'fileCache' => true, // 启用 bean 文件缓存；修改代码不生效时，需要删除 `.runtime/classes` 目录缓存
        ],
    ],
];
```

## 命令

启动开发服务: `vendor/bin/imi-cli fpm/start`

> 此命令仅限于开发使用，正式环境推荐配合 Nginx 或 Apache 运行
