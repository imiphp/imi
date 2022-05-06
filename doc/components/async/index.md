# 异步执行

[toc]

imi v2.0.7 支持在方法上使用 `@Async` 注解，让这个方法被正常调用时是异步调用。

`@Async` 注解类：`\Imi\Async\Annotation\Async`

> 此特性所有环境都可使用，但仅在 Swoole 下才是真异步

## 使用示例

### 异步执行无返回值

**定义：**

```php
<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Async;

use Imi\Async\Annotation\Async;
use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncResult;
use Imi\Bean\Annotation\Bean;

class AsyncTester
{
    /**
     * @Async
     */
    public function test1(): void
    {
        // 这里的代码是异步执行的
        sleep(1);
    }
}
```

**调用：**

```php
$asyncTester = \Imi\App::getBean(AsyncTester::class);
$result = $asyncTester->test1();
// 下面的代码会立即执行，而不是等待 1 秒后
// ...
$result->get(); // 等待异步执行完毕
$result->get(0.1); // 等待异步执行完毕，超时时间为 0.1 秒，超时则抛出异常
```

### 异步执行有返回值

**定义：**

```php
<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Async;

use Imi\Async\Annotation\Async;
use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncResult;
use Imi\Bean\Annotation\Bean;

class AsyncTester
{
    /**
     * 如果一定要声明方法返回值类型，必须声明为 IAsyncResult
     * 
     * @Async
     */
    public function test2(float $a, float $b): IAsyncResult
    {
        return new AsyncResult($a + $b);
    }

    /**
     * 不声明方法返回值类型也可以
     * 
     * @Async
     *
     * @return float|IAsyncResult
     */
    public function test3(float $a, float $b)
    {
        return $a + $b;
    }
}
```

**调用：**

```php
$asyncTester = \Imi\App::getBean(AsyncTester::class);
$asyncTester->test2(1, 2)->get(); // 3
$asyncTester->test3(1, 2)->get(); // 3
```

### 捕获异常

**调用：**

```php
$asyncTester = \Imi\App::getBean(AsyncTester::class);
try {
    $asyncTester->test1()->get(0.01);
} catch (\Imi\Async\Exception\AsyncTimeoutException $te) {
    // 捕获异步超时异常
} catch (\Throwable $th) {
    // 捕获执行期间其它异常
}
```
