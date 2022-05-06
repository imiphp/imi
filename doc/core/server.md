# Server 对象

[toc]

每个服务器、每个协议端口，在 imi 中都有一个 Server 对象。

## 如何拿到 Server 对象

在控制器中:

```php
$this->server
```

在任意地方:

```php
\Imi\RequestContext::getServer();
```

## 公共方法

```php
/**
 * 获取服务器名称.
 */
public function getName(): string;

/**
 * 获取协议名称.
 */
public function getProtocol(): string;

/**
 * 获取配置信息.
 */
public function getConfig(): array;

/**
 * 获取容器对象
 */
public function getContainer(): Container;

/**
 * 获取Bean对象
 *
 * @param mixed $params
 */
public function getBean(string $name, ...$params): object;

/**
 * 是否为长连接服务
 */
public function isLongConnection(): bool;

/**
 * 是否支持 SSL.
 */
public function isSSL(): bool;

/**
 * 开启服务
 */
public function start(): void;

/**
 * 终止服务
 */
public function shutdown(): void;

/**
 * 重载服务
 */
public function reload(): void;

/**
 * 调用服务器方法.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
public function callServerMethod(string $methodName, ...$args);
```

## Swoole Server 独有方法

```php
/**
 * 获取 swoole 服务器对象
 */
public function getSwooleServer(): Server;

/**
 * 获取 swoole 监听端口.
 */
public function getSwoolePort(): Port;

/**
 * 是否为子服务器.
 */
public function isSubServer(): bool;

/**
 * 组是否存在.
 */
public function hasGroup(string $groupName): bool;

/**
 * 创建组，返回组对象
 */
public function createGroup(string $groupName, int $maxClients = -1): Group;

/**
 * 获取组对象，不存在返回null.
 */
public function getGroup(string $groupName): ?Group;

/**
 * 加入组，组不存在则自动创建.
 *
 * @param int|string $clientId
 */
public function joinGroup(string $groupName, $clientId): void;

/**
 * 离开组，组不存在则自动创建.
 *
 * @param int|string $clientId
 */
public function leaveGroup(string $groupName, $clientId): void;

/**
 * 调用组方法.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
public function groupCall(string $groupName, string $methodName, ...$args);

/**
 * 获取所有组列表.
 *
 * @return \Imi\Server\Group\Group[]
 */
public function getGroups(): array;
```

## Workerman Server 独有方法

```php
/**
 * 获取 Workerman Worker 对象
 */
public function getWorker(): Worker;

/**
 * 组是否存在.
 */
public function hasGroup(string $groupName): bool;

/**
 * 创建组，返回组对象
 */
public function createGroup(string $groupName, int $maxClients = -1): Group;

/**
 * 获取组对象，不存在返回null.
 */
public function getGroup(string $groupName): ?Group;

/**
 * 加入组，组不存在则自动创建.
 *
 * @param int|string $clientId
 */
public function joinGroup(string $groupName, $clientId): void;

/**
 * 离开组，组不存在则自动创建.
 *
 * @param int|string $clientId
 */
public function leaveGroup(string $groupName, $clientId): void;

/**
 * 调用组方法.
 *
 * @param mixed ...$args
 *
 * @return mixed
 */
public function groupCall(string $groupName, string $methodName, ...$args);

/**
 * 获取所有组列表.
 *
 * @return \Imi\Server\Group\Group[]
 */
public function getGroups(): array;
```

## PHP-FPM Server 独有方法

无
