<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Bean\IBean;
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
            if (is_subclass_of(static::class, IBean::class))
            {
                $realClassNames[static::class] = get_parent_class(static::class);
            }
            else
            {
                $realClassNames[static::class] = static::class;
            }
        }

        return $realClassNames[static::class];
    }
}
