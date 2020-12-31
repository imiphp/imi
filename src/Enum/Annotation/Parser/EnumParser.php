<?php

declare(strict_types=1);

namespace Imi\Enum\Annotation\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Enum\Annotation\EnumItem;

class EnumParser extends BaseParser
{
    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     *
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        if ($annotation instanceof EnumItem)
        {
            $value = \constant($className . '::' . $targetName);
            $data = &$this->data;
            $data['map'][$className][$targetName] = $value;
            $data['EnumItem'][$className][$value] = $annotation;
        }
    }

    /**
     * 获得枚举项.
     *
     * @param string $className
     * @param mixed  $value
     *
     * @return \Imi\Enum\Annotation\EnumItem|null
     */
    public function getEnumItem(string $className, $value): ?EnumItem
    {
        return $this->data['EnumItem'][$className][$value] ?? null;
    }

    /**
     * 获取常量名=>值集合.
     *
     * @param string $className
     *
     * @return string[]
     */
    public function getMap(string $className): array
    {
        return $this->data['map'][$className] ?? [];
    }

    /**
     * 获取所有名称.
     *
     * @param string $className
     *
     * @return string[]
     */
    public function getNames(string $className): array
    {
        return array_keys($this->data['map'][$className] ?? []);
    }

    /**
     * 获取所有值
     *
     * @param string $className
     *
     * @return array
     */
    public function getValues(string $className): array
    {
        $data = &$this->data;
        if (isset($data['EnumItem'][$className]))
        {
            return array_keys($data['EnumItem'][$className]);
        }
        else
        {
            return [];
        }
    }
}
