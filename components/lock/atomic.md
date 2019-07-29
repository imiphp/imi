# AtomicLock

AtomicLock 是单机进程锁，会阻塞。

使用前需要配置 Atomic。

> 注意！会阻塞当前进程，不建议在 worker 进程中使用！

类：`Imi\Lock\Handler\Atomic`

Bean 名：`AtomicLock`

## 可配置参数

```php
/**
 * 锁的唯一 ID
 *
 * @var string
 */
protected $id;

/**
 * 等待锁超时时间，单位：毫秒，0为不限制
 * 
 * @var int
 */
protected $waitTimeout = 3000;

/**
 * 锁超时时间，单位：毫秒
 * 
 * @var int
 */
protected $lockExpire = 3000;

/**
 * 配置的 Atomic 名称
 *
 * @var string
 */
public $atomicName;

/**
 * 同时获得锁的数量
 * 
 * @var int
 */
public $quantity = 1;

```

## 配置示例

```php
// 锁
'lock'  =>[
    'list'  =>  [
        'atomic' =>  [
            'class' =>  'AtomicLock',
            'options'   =>  [
                'atomicName'    =>  'atomicLock',
            ],
        ],
    ],
],
```
