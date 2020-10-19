<?php

namespace Imi\Bean\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\ReflectionContainer;
use Imi\Util\LazyArrayObject;

/**
 * 注解基类.
 */
abstract class Base extends LazyArrayObject
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName;

    public function __construct($data = [])
    {
        parent::__construct([]);

        if (null !== $this->defaultFieldName && \array_key_exists('value', $data) && 1 === \count($data))
        {
            // 只传一个参数处理
            $this->{$this->defaultFieldName} = $data['value'];
        }
        else
        {
            foreach ($data as $k => $v)
            {
                $this->$k = $v;
            }
        }

        $refClass = ReflectionContainer::getClassReflection(static::class);

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property)
        {
            $propertyName = $property->name;
            $value = $this->$propertyName;
            unset($this->$propertyName);
            $this->$propertyName = $value;
        }
    }

    public function &offsetGet($offset)
    {
        $value = parent::offsetGet($offset);
        if ($value instanceof BaseInjectValue)
        {
            $value = $value->getRealValue();
        }

        return $value;
    }

    public function __wakeup()
    {
        $refClass = ReflectionContainer::getClassReflection(static::class);
        foreach ($refClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property)
        {
            unset($this->{$property->name});
        }
    }
}
