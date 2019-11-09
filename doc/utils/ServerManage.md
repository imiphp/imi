# ServerManage

类名：`Imi\ServerManage`

imi 支持服务监听多个端口、多个协议，该类用于管理监听端口的服务。

### 可用方法

```php
/**
 * 获取服务器数组
 * @return \Imi\Server\Base[]
 */
public static function getServers();

/**
 * 获取服务器对象
 * @param string $name
 * @return \Imi\Server\Base
 */
public static function getServer($name);
```

> `$name` 参数是在配置文件中，定义的服务器名称。如果是主服务，强制为：`main`

### 获取 Swoole 服务器对象

```php
$swooleServer = ServerManage::getServer('main')->getSwooleServer();
```