# Session

imi 的 Http Session 目前内嵌支持文件和 Redis 两种存储方式，当然你也可以自行扩展更多存储方式。

如果想要启用 Session，需要在配置文件中进行设置。

## 配置

在服务器配置文件中：

```php
return [
    'beans'    =>    [
        'SessionManager'    =>    [
            // 指定 Session 存储驱动类
            'handlerClass'    =>    \Imi\Server\Session\Handler\File::class,
        ],
        'SessionConfig'    =>    [
            // session 名称，默认为imisid
            'name'    =>    'imisid',
            // 每次请求完成后触发垃圾回收的概率，默认为1%，可取值0~1.0，概率为0%~100%
            'gcProbability'    =>    0.01,
            // 最大存活时间，默认30天，单位秒
            'maxLifeTime'=>    86400 * 30,
            // session 前缀
            'prefix' => null,
        ],
        'SessionCookie'    =>    [
            // 是否启用 Cookie
            'enable'    =>  true,
            // Cookie 的 生命周期，以秒为单位。
            'lifetime'    =>    0,
            // 此 cookie 的有效 路径。 on the domain where 设置为“/”表示对于本域上所有的路径此 cookie 都可用。
            'path'        =>    '/',
            // Cookie 的作用 域。 例如：“www.php.net”。 如果要让 cookie 在所有的子域中都可用，此参数必须以点（.）开头，例如：“.php.net”。
            'domain'    =>    '',
            // 设置为 TRUE 表示 cookie 仅在使用 安全 链接时可用。
            'secure'    =>    false,
            // 设置为 TRUE 表示 PHP 发送 cookie 的时候会使用 httponly 标记。
            'httponly'    =>    false,
        ],
        // 配置中间件
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                // Session 中间件
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
            ],
        ],
    ],
];
```

其中每一种存储方式还有特别的配置项，请看下文。

### 文件

服务器配置文件->beans中加入：

```php
'beans'    =>    [
    'SessionFile'    =>    [
        'savePath'    =>    'Session文件存储路径',
    ]
]
```

> 文件 Session 未来不支持分布式，推荐使用 Redis！

### Redis

```php
'beans'    =>    [
    'SessionRedis'    =>    [
        // Redis连接池名称
        'poolName'    =>    '',
        // Redis中存储的key前缀，可以用于多系统session的分离
        // 'keyPrefix'    =>    'imi:',
    ]
]
```

## Session 存储序列化方式配置

根据你选用的存储驱动类，配置在对应的节里，下面以文件驱动演示

```php
'beans'    =>    [
    'SessionFile'    =>    [
        'formatHandlerClass'    =>    \Imi\Util\Format\PhpSerialize::class,
    ]
]
```

可以选用的序列化类：

JSON:`\Imi\Util\Format\Json::class`

PHP序列化:`\Imi\Util\Format\PhpSerialize::class`

PHP Session 序列化:`\Imi\Util\Format\PhpSession::class` （兼容 php-fpm 默认的 Session 存储格式）

## 使用

### 引入 Session 类

```php
use Imi\Server\Session\Session;
```

### 读取

```php
// 获取值
Session::get('aaa');
// 获取值，如果不存在则返回默认值
Session::get('aaa', 'default value');
// 获取$session['a']['b']的值
Session::get('a.b');
// 获取$session[前缀]['aaa']的值，前缀在配置文件中设置
Session::get('@.aaa');
```

### 写入

```php
Session::set($name, $value)
```

### 删除

```php
Session::delete($name)
```

### 读取并删除

```php
Session::once($name, $default = false)
```

### 清空

```php
Session::clear();
```

### 自定义 Session ID 获取方式

config.php:

```php
[
    'beans'    =>    [
        \Imi\Server\Session\Middleware\HttpSessionMiddleware::class => [
            'sessionIdHandler'    =>    function(\Imi\Server\Http\Message\Request $request){
                // 举例，比如希望从 header 中获取
                return $request->getHeaderLine('X-Session-ID');
            },
        ],
    ],
]
```
