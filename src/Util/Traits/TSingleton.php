<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

/**
 * 单例模式.
 */
trait TSingleton
{
    /**
     * 实例对象
     *
     * @var object|null
     */
    protected static ?object $instance = null;

    /**
     * 实例对象数组.
     *
     * @var array
     */
    protected static array $instances = [];

    private function __construct()
    {
    }

    /**
     * 获取单例对象
     *
     * @param array ...$args
     *
     * @return static
     */
    public static function getInstance(...$args)
    {
        if (static::isChildClassSingleton())
        {
            $className = static::class;
            $instances = &static::$instances;
            if (isset($instances[$className]))
            {
                return $instances[$className];
            }
            else
            {
                return $instances[$className] = new static(...$args);
            }
        }
        else
        {
            if (null === static::$instance)
            {
                static::$instance = new static(...$args);
            }

            return static::$instance;
        }
    }

    /**
     * 是否子类作为单独实例.
     *
     * @return bool
     */
    protected static function isChildClassSingleton(): bool
    {
        return false;
    }
}
