# 配置(Config)

关于配置文件的定义，请看基础入门-配置文件章节。

## 读取配置

类：`Imi\Config`

### 读取项目配置

```php
echo Config::get('@app.namespace'); // namespace 可以换为其它的节
```

### 读取主服务器配置

```php
echo Config::get('@app.mainServer.namespace'); // namespace 可以换为其它的节
```

### 读取子服务器配置

```php
// 如子服务器名为abc，读取它下面的配置
echo Config::get('@server_abc.namespace'); // namespace 可以换为其它的节
```