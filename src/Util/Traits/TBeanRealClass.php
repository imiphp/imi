<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Bean\IBean;
use Imi\Bean\ReflectionContainer;
use Imi\Util\StaticValueStorage;

trait TBeanRealClass
{
    /**
     * 获取当前Bean类真实类名.
     */
    protected static function __getRealClassName(): string
    {
        $realClassNames = &StaticValueStorage::$realClassNames;
        if (!isset($realClassNames[static::class]))
        {
            $ref = ReflectionContainer::getClassReflection(static::class);
            if ($ref->implementsInterface(IBean::class))
            {
                $realClassNames[static::class] = $ref->getParentClass()->getName();
            }
            else
            {
                $realClassNames[static::class] = $ref->getName();
            }
        }

        return $realClassNames[static::class];
    }
}
