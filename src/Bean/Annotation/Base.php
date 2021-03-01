<?php

declare(strict_types=1);

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
     * @var string|null
     */
    protected ?string $defaultFieldName = null;

    /**
     * 注解别名.
     *
     * @var string|string[]
     */
    protected $__alias;

    public function __construct(?array $data = null, ...$args)
    {
        parent::__construct([]);

        $refClass = ReflectionContainer::getClassReflection(static::class);

        if (null === $data)
        {
            if ($args)
            {
                $argsCount = \count($args);
                $params = $refClass->getConstructor()->getParameters();
                $count = \count($params);
                $forCount = min($count, $argsCount);
                for ($i = 0; $i < $forCount; ++$i)
                {
                    $param = $params[$i + 1];
                    $this->{$param->getName()} = $args[$i];
                }
            }
        }
        else
        {
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
        }

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

    public function __serialize(): array
    {
        return [
            $this->defaultFieldName,
            $this->__alias,
            $this->toArray(),
        ];
    }

    public function __unserialize(array $data): void
    {
        $refClass = ReflectionContainer::getClassReflection(static::class);
        foreach ($refClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property)
        {
            unset($this->{$property->name});
        }
        [$this->defaultFieldName, $this->__alias, $dataMap] = $data;
        foreach ($dataMap as $k => $v)
        {
            $this[$k] = $v;
        }
    }

    /**
     * Get the value of alias.
     *
     * @return string|string[]
     */
    public function getAlias()
    {
        return $this->__alias;
    }
}
