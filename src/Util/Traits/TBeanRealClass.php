<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Bean\IBean;
use Imi\Bean\ReflectionContainer;

trait TBeanRealClass
{
    /**
     * 真实类名集合.
     */
    public static array $realClassNames = [];

    /**
     * 获取当前Bean类真实类名.
     */
    protected static function __getRealClassName(): string
    {
        if (!isset(TBeanRealClass::$realClassNames[static::class]))
        {
            $ref = ReflectionContainer::getClassReflection(static::class);
            if ($ref->implementsInterface(IBean::class))
            {
                TBeanRealClass::$realClassNames[static::class] = $ref->getParentClass()->getName();
            }
            else
            {
                TBeanRealClass::$realClassNames[static::class] = $ref->getName();
            }
        }

        return TBeanRealClass::$realClassNames[static::class];
    }
}
