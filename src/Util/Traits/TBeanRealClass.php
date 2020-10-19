<?php

namespace Imi\Util\Traits;

use Imi\Bean\IBean;
use Imi\Bean\ReflectionContainer;

trait TBeanRealClass
{
    /**
     * 真实类名集合.
     *
     * @var array
     */
    public static $realClassNames;

    /**
     * 获取当前Bean类真实类名.
     *
     * @return string
     */
    protected static function __getRealClassName()
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
