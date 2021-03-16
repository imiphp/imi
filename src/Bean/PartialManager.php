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

    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     */
    public static function add(string $partialClass, string $targetClass): void
    {
        if (!isset(self::$map[$targetClass]) || !\in_array($partialClass, self::$map[$targetClass]))
        {
            self::$map[$targetClass][] = $partialClass;
        }
    }

    /**
     * 获取目标类的 partial trait 列表.
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
