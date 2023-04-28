# SSE

## SSE 介绍

SSE 是一种服务端主动向客户端（浏览器）推送数据的技术。

服务端向客户端发送一个响应头：`Content-Type: text/event-stream`

然后服务端按如下格式发送数据：

```text
: 注释
data: 数据\n
event: 事件\n
id: id值\n
retry: 重试时间间隔，单位：秒\n\n
```

> 其中每一行都是非必传项，每一行必须以 `\n` 结尾

> `\n\n` 代表一次推送的结束

## 使用示例

```php
use Imi\Server\Http\Message\Emitter\SseEmitter;
use Imi\Server\Http\Message\Emitter\SseMessageEvent;

/**
 * SSE.
 *
 * @Action
 */
public function sse(): void
{
    $this->response->setResponseBodyEmitter(new class() extends SseEmitter {
        protected function task(): void
        {
            $handler = $this->getHandler();
            // 模拟推送数据
            foreach (range(1, 100) as $i)
            {
                // 推送数据
                $handler->send((string) new SseMessageEvent((string) $i));
                usleep(10000);
            }
        }
    });
}
```

### SseMessageEvent

`Imi\Server\Http\Message\Emitter\SseMessageEvent` 类是 SSE 推送事件类，构造方法参数如下：

```php
public function __construct(
    ?string $data = null,
    ?string $event = null,
    ?string $id = null,
    ?int $retry = null,
    ?string $comment = null
)
```
