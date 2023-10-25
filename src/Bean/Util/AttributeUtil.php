<?php

declare(strict_types=1);

namespace Imi\Bean\Util;

use Imi\Util\Traits\TStaticClass;

class AttributeUtil
{
    use TStaticClass;

    public static function generateAttributesCode(object|array $attributes, int $level = 0): string
    {
        $attributeCodes = [];
        $tab1 = str_repeat('    ', $level + 1);
        $tab2 = str_repeat('    ', $level + 2);
        foreach (\is_array($attributes) ? $attributes : [$attributes] as $attribute)
        {
            $ref = new \ReflectionClass($attribute);
            if (!$ref->getAttributes(\Attribute::class))
            {
                throw new \InvalidArgumentException(sprintf('Class %s does not an Attribute', $ref->name));
            }
            $constructor = $ref->getConstructor();
            $props = [];
            foreach ($attribute as $key => $value)
            {
                if ($ref->hasProperty($key))
                {
                    $prop = $ref->getProperty($key);
                    if ($prop->hasDefaultValue())
                    {
                        if ($prop->getDefaultValue() === $value)
                        {
                            continue;
                        }
                    }
                }
                // 构造方法属性提升，无法获取到属性默认值，所以需要获取构造方法参数处理
                if ($constructor)
                {
                    foreach ($constructor->getParameters() as $param)
                    {
                        if ($param->name === $key)
                        {
                            if ($param->isDefaultValueAvailable() && $param->getDefaultValue() === $value)
                            {
                                continue 2;
                            }
                            break;
                        }
                    }
                }
                if (\is_object($value))
                {
                    $value = self::generateAttributesCode([$value], $level + 1);
                }
                elseif (\is_array($value))
                {
                    $newValue = [];
                    foreach ($value as $itemKey => $itemValue)
                    {
                        if (\is_object($itemValue))
                        {
                            $newValue[$itemKey] = self::generateAttributesCode($itemValue, $level + 1);
                        }
                        else
                        {
                            $newValue[$itemKey] = $tab2 . var_export($itemValue, true);
                        }
                    }
                    $value = '[' . \PHP_EOL . implode(',' . \PHP_EOL, $newValue) . \PHP_EOL . $tab1 . ']';
                }
                else
                {
                    $value = var_export($value, true);
                }
                $props[] = $key . ': ' . $value;
            }
            $attributeCodes[] = $tab1 . ($level ? 'new ' : '') . '\\' . $ref->name . '(' . implode(', ', $props) . ')';
        }

        $result = implode(',' . \PHP_EOL, $attributeCodes);
        if (\is_object($attributes))
        {
            if (0 === $level)
            {
                $result = '#[' . \PHP_EOL . $result . \PHP_EOL . str_repeat('    ', $level) . ']';
            }
        }
        else
        {
            $result = '[' . \PHP_EOL . $result . \PHP_EOL . str_repeat('    ', $level) . ']';
            if (0 === $level)
            {
                $result = '#' . $result;
            }
        }

        return $result;
    }
}
