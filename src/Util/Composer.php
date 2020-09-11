<?php

namespace Imi\Util;

use Composer\Autoload\ClassLoader;

/**
 * Composer 工具类.
 */
class Composer
{
    private function __construct()
    {
    }

    /**
     * 获取 Composer ClassLoader 对象
     *
     * @return \Composer\Autoload\ClassLoader|null
     */
    public static function getClassLoader(): ?ClassLoader
    {
        foreach (get_declared_classes() as $class)
        {
            if (Text::startwith($class, 'ComposerAutoloaderInit'))
            {
                return $class::getLoader();
            }
        }
    }
}
