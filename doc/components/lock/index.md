# 锁

[toc]

锁（Lock），在并发处理，防止冲突的场景非常常用。

在 imi 中，你可以使用注解，或者自己实例化Lock类来实现加锁处理。

除了内置的锁驱动外，你可以实现`Imi\Lock\Handler\ILockHandler`接口，来实现其他方式的锁。

## 配置用法

> 需要在配置中预定义

### 配置说明

```php
// 锁
'lock'  =>[
    'list'  =>  [
        // 锁 ID => 配置
        'redis' =>  [
            'class' =>  'RedisLock', // Handler 类 Bean 名或完整类名
            'options'   =>  [
                // Handler 类所需配置
                'poolName'  =>  'redis_test',
            ],
        ],
    ],
],
```

### 使用说明

顺序用法：

```php
use Imi\Lock\Lock;
$lockId = ''; // 你定义的ID
if(Lock::lock($lockId))
{
    try {
        // 干一些事情
    } catch(\Throwable $th) {
        throw $th;
    } finally {
        Lock::unlock($lockId);
    }
}
```

回调用法（无需手动释放锁）：

```php
$result = Lock::lock($lockId, function(){
    // 执行任务
}, function(){
    // return 非null则不执行任务
    // 一般用于防止缓存击穿
    // 这个回调可以不传
});
if($result)
{
    // 加锁并执行成功
}
else
{
    // 加锁失败
}
```

获取 Handler 对象：

```php
$lock = Lock::getInstance($lockId);
$lock->lock(); // 使用方法参考下面的“实例化使用方法”
```

## 注解使用

`@Lockable`

> 无需在配置中预定义

支持参数：

```php
/**
 * 锁ID
 * 支持{id}、{data.name}形式，代入参数
 * 如果为null，则使用类名+方法名+全部参数，序列化后hash
 *
 * @var string|null
 */
public $id;

/**
 * 锁类型，如：RedisLock
 * 为null则使用默认锁类型（@currentServer.lock.defaultType）
 *
 * @var string|null
 */
public $type;

/**
 * 等待锁超时时间，单位：毫秒，0为不限制
 * 
 * @var int
 */
public $waitTimeout = 3000;

/**
 * 锁超时时间，单位：毫秒
 * 
 * @var int
 */
public $lockExpire = 3000;

/**
 * 锁初始化参数
 *
 * @var array
 */
public $options = [];

/**
 * 当获得锁后执行的回调。该回调返回非 null 则不执行加锁后的方法，本回调的返回值将作为返回值
 * 一般用于防止缓存击穿，获得锁后再做一次检测
 * 如果为{"$this", "methodName"}格式，$this将会被替换为当前类，方法必须为 public 或 protected
 * 
 * @var callable
 */
public $afterLock;

/**
 * 允许注解引用配置文件中相同锁id的配置
 * 当该选项为真且声明`LockId`情况下，将尝试从配置文件加载相同`id`的配置，但仍然以注解定义的值为首选值。
 * 
 * @var bool
 */
public $useConfig = true;

/**
 * 执行超时抛出异常。
 * 
 * @var bool
 */
public $timeoutException = false;

/**
 * 解锁失败抛出异常。
 * 
 * @var bool
 */
public $unlockException = false;
```

### 用法示例

最简单的锁：

只指定id，其它全部默认值

```php
@Lockable(id="锁ID")
```

定义执行任务前的操作：

```php
class Test
{
    /**
     * @Lockable(id="锁ID", afterLock={"$this", "check"})
     */
    public function index()
    {
        return 1;
    }

    protected function check()
    {
        return 2;
    }

    /**
     * @Lockable(id="锁ID", afterLock={"$this", "check2"})
     */
    public function index2()
    {
        return 3;
    }

    protected function check2()
    {
        
    }
}

$result = App::getBean('Test')->index();
echo $result, PHP_EOL; // 2

$result = App::getBean('Test')->index2();
echo $result, PHP_EOL; // 3
```

## 实例化使用方法

> 无需在配置中预定义

### 顺序用法

```php
$redisLock = new \Imi\Lock\Handler\Redis('锁ID', [
    'poolName'  => 'redis', // Redis 连接池名称，默认取redis.default配置
    'db'        =>  null, // Redis 几号库，为null或不配置则使用连接池中的设置
    'waitSleepTime' =>  20, // 获得锁每次尝试间隔，单位：毫秒
    'keyPrefix' =>  'imi:lock:', // Redis key 前置
]);

if($redisLock->lock())
{
    // 加锁后的处理

    // 解锁
    $redisLock->unlock();
}
else
{
    // 加锁失败的处理
}
```

### 回调用法

```php
$redisLock = new \Imi\Lock\Handler\Redis('锁ID', [
    'poolName'  => 'redis', // Redis 连接池名称，默认取redis.default配置
    'db'        =>  null, // Redis 几号库，为null或不配置则使用连接池中的设置
    'waitSleepTime' =>  20, // 获得锁每次尝试间隔，单位：毫秒
    'keyPrefix' =>  'imi:lock:', // Redis key 前置
]);

// 执行后自动解锁
$result = $redisLock->lock(function(){
    // 执行任务
}, function(){
    // return 非null则不执行任务
    // 一般用于防止缓存击穿
});

if($result)
{
    // 加锁并执行成功
}
else
{
    // 加锁失败
}
```