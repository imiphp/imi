<?php
namespace Imi\Util\Traits;

use Imi\Bean\IBean;
use Imi\Util\ClassObject;


trait TBeanRealClass
{
    protected static $realClassName;

    protected static function __getRealClassName()
    {
        if(null === static::$realClassName)
        {
            $ref = new \ReflectionClass(static::class);
            if($ref->implementsInterface(IBean::class))
            {
                static::$realClassName = $ref->getParentClass()->getName();
            }
            else
            {
                static::$realClassName = $ref->getName();
            }
        }
        return static::$realClassName;
    }
}