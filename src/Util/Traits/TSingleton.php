<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

/**
 * 单例模式.
 */
trait TSingleton
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 实例对象
     */
    protected static ?object $__instance = null;

    /**
     * 实例对象数组.
     */
    protected static array $__instances = [];

    /**
     * 获取单例对象
     *
     * @return static
     */
    public static function getInstance(mixed ...$args): object
    {
        if (static::isChildClassSingleton())
        {
            $instances = &static::$__instances;
            if (isset($instances[static::class]))
            {
                return $instances[static::class];
            }
            else
            {
                // @phpstan-ignore-next-line
                return $instances[static::class] = new static(...$args);
            }
        }
        else
        {
            if (null === static::$__instance)
            {
                // @phpstan-ignore-next-line
                static::$__instance = new static(...$args);
            }

            // @phpstan-ignore-next-line
            return static::$__instance;
        }
    }

    /**
     * 是否子类作为单独实例.
     */
    protected static function isChildClassSingleton(): bool
    {
        return false;
    }
}
