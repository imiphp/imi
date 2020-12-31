<?php

declare(strict_types=1);

namespace Imi\Enum;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\Annotation\Parser\EnumParser;

abstract class BaseEnum
{
    /**
     * 根据值，获取常量名，失败返回null.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    public static function getName($value): ?string
    {
        $map = EnumParser::getInstance()->getMap(static::class);
        $key = array_search($value, $map);

        return $key ?? null;
    }

    /**
     * 获取值
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getValue(string $name)
    {
        return \constant(static::class . '::' . $name);
    }

    /**
     * 获取文本.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    public static function getText($value): ?string
    {
        $enumItem = EnumParser::getInstance()->getEnumItem(static::class, $value);
        if ($enumItem)
        {
            return $enumItem->text;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取注解.
     *
     * @param mixed $value
     *
     * @return \Imi\Enum\Annotation\EnumItem|null
     */
    public static function getData($value): ?EnumItem
    {
        return EnumParser::getInstance()->getEnumItem(static::class, $value);
    }

    /**
     * 获取所有名称.
     *
     * @return string[]
     */
    public static function getNames(): array
    {
        return EnumParser::getInstance()->getNames(static::class);
    }

    /**
     * 获取所有值
     *
     * @return array
     */
    public static function getValues(): array
    {
        return EnumParser::getInstance()->getValues(static::class);
    }

    /**
     * 获取键值对应数组.
     *
     * @return array
     */
    public static function getMap(): array
    {
        return EnumParser::getInstance()->getMap(static::class);
    }
}
