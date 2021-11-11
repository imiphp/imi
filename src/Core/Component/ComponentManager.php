<?php

declare(strict_types=1);

namespace Imi\Core\Component;

use Imi\Main\Helper;

class ComponentManager
{
    private static array $components = [];

    private function __construct()
    {
    }

    public static function addComponent(string $name, string $namespace): void
    {
        self::$components[$name] = $namespace;
        Helper::getMain($namespace, $name);
    }

    public static function getComponents(): array
    {
        return self::$components;
    }

    public static function setComponents(array $components): void
    {
        self::$components = $components;
    }

    public static function has(string $name): bool
    {
        return isset(self::$components[$name]);
    }
}
