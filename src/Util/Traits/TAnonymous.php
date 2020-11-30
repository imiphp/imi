<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Util\ClassObject;

trait TAnonymous
{
    protected static function __getRealClassName()
    {
        if (ClassObject::isAnymous(static::class))
        {
            return get_parent_class(static::class);
        }
        else
        {
            return static::class;
        }
    }
}
