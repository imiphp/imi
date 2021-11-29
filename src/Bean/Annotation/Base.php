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
     */
    protected ?string $defaultFieldName = null;

    /**
     * 注解别名.
     *
     * @var string|string[]
     */
    protected $__alias;

    /**
     * @param mixed ...$__args
     */
    public function __construct(?array $__data = null, ...$__args)
    {
        $data = $__data ?? [];
        $params = ReflectionContainer::getClassReflection(static::class)->getConstructor()->getParameters();
        foreach ($params as $i => $param)
        {
            $name = $param->name;
            if ('__data' === $name || '__args' === $name)
            {
                continue;
            }
            if ($__data && \array_key_exists($name, $__data))
            {
                continue;
            }
            if (isset($__args[$i - 1]))
            {
                $data[$name] = $__args[$i - 1];
            }
            else
            {
                $data[$name] = $param->getDefaultValue();
            }
        }
        $defaultFieldName = $this->defaultFieldName;
        if ($__data && null !== $defaultFieldName && 'value' !== $defaultFieldName && 1 === \count($__data))
        {
            if (\array_key_exists('value', $__data))
            {
                // 只传一个参数处理
                $data[$defaultFieldName] = $__data['value'];
                unset($data['value']);
            }
            elseif ($i >= 1 && array_is_list($__data))
            {
                $data[$params[1]->name] = $__data[0];
            }
        }

        parent::__construct($data);
    }

    /**
     * @param int|string $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function &offsetGet($offset)
    {
        $value = parent::offsetGet($offset);
        if ($value instanceof BaseInjectValue)
        {
            $value = $value->getRealValue();
        }

        return $value;
    }

    /**
     * @return string|string[]
     */
    public function getAlias()
    {
        return $this->__alias;
    }
}
