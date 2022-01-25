<?php

declare(strict_types=1);

namespace Imi\Macro;

use Composer\Autoload\ClassLoader;

class MacroComposerHook
{
    private function __construct()
    {
    }

    public static function hookComposer(): void
    {
        $autoLoaders = spl_autoload_functions();

        // Proxy the composer class loader
        foreach ($autoLoaders as &$autoLoader)
        {
            $unregisterAutoloader = $autoLoader;
            if (\is_array($autoLoader) && isset($autoLoader[0]) && $autoLoader[0] instanceof ClassLoader)
            {
                $autoLoader[0] = new AutoLoader($autoLoader[0]);
            }
            spl_autoload_unregister($unregisterAutoloader);
        }

        unset($autoLoader);

        // Re-register the loaders
        foreach ($autoLoaders as $autoLoader)
        {
            spl_autoload_register($autoLoader);
        }
    }
}
