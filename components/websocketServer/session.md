# 会话数据

IMI 中 WebSocket 服务中使用 `Imi\ConnectContext` 类对连接的会话数据进行管理。

## 使用

```php
use Imi\ConnectContext;

// 取值
echo ConnectContext::get('name');
echo ConnectContext::get('name', 'default value');

// 赋值
ConnectContext::set('name', 'value');

// 获取所有数据
$array = ConnectContext::getContext();
```

