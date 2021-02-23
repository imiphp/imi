<?php

declare(strict_types=1);

namespace Imi\Bean;

/**
 * Partial 管理器.
 */
class PartialManager
{
    private static array $map = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map)
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     *
     * @param string $partialClass
     * @param string $targetClass
     *
     * @return void
     */
    public static function add(string $partialClass, string $targetClass)
    {
        self::$map[$targetClass][] = $partialClass;
    }

    /**
     * 获取目标类的 partial trait 列表.
     *
     * @param string $class
     *
     * @return array
     */
    public static function getClassPartials(string $class): array
    {
        $classes = class_parents($class);
        if (isset($classes[1]))
        {
            $classes = array_reverse($classes);
        }
        $classes[] = $class;

        $traits = [];
        $partialData = self::$map;
        foreach ($classes as $currentClass)
        {
            if (isset($partialData[$currentClass]))
            {
                $traits[] = $partialData[$currentClass];
            }
        }

        return $traits;
    }
}
