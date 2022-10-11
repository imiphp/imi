<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Bean\BeanFactory;
use Imi\Util\StaticValueStorage;

trait TBeanRealClass
{
    /**
     * 获取当前Bean类真实类名.
     */
    protected static function __getRealClassName(): string
    {
        return StaticValueStorage::$realClassNames[static::class] ??= BeanFactory::getObjectClass(static::class);
    }
}
