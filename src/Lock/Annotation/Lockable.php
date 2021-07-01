<?php

declare(strict_types=1);

namespace Imi\Lock\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 锁注解.
 *
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null   $id          锁ID；支持{id}、{data.name}形式，代入参数；如果为null，则使用类名+方法名+全部参数，序列化后hash
 * @property string|null   $type        锁类型，如：RedisLock；为null则使用默认锁类型（@currentServer.lock.defaultType）
 * @property int           $waitTimeout 等待锁超时时间，单位：毫秒，0为不限制
 * @property int           $lockExpire  锁超时时间，单位：毫秒
 * @property array         $options     锁初始化参数
 * @property callable|null $afterLock   当获得锁后执行的回调。该回调返回非 null 则不执行加锁后的方法，本回调的返回值将作为返回值；一般用于防止缓存击穿，获得锁后再做一次检测；如果为{"$this", "methodName"}格式，$this将会被替换为当前类，方法必须为 public 或 protected
 */
#[\Attribute]
class Lockable extends Base
{
    public function __construct(?array $__data = null, ?string $id = null, ?string $type = null, int $waitTimeout = 3000, int $lockExpire = 3000, array $options = [], ?callable $afterLock = null)
    {
        parent::__construct(...\func_get_args());
    }
}
