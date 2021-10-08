<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

(function () {
    global $COMPONENTS_NS;
    $COMPONENTS_NS = [];

    $componentsDir = dirname(__DIR__) . '/src/Components';
    foreach (new FilesystemIterator($componentsDir, FilesystemIterator::SKIP_DOTS) as $dir)
    {
        /** @var SplFileInfo $dir */
        if (!$dir->isDir())
        {
            continue;
        }
        $fileName = $dir->getPathname() . '/vendor/autoload.php';
        if (is_file($fileName))
        {
            /** @var ClassLoader $loader */
            $loader = require_once $fileName;

            // 预解析组件命名空间
            $name = $dir->getBasename();
            foreach ($loader->getPrefixesPsr4() as $namespace => $paths)
            {
                foreach ($paths as $path)
                {
                    $srcDir = realpath($path);
                    if ($srcDir && str_ends_with(realpath($srcDir), $dir->getBasename() . \DIRECTORY_SEPARATOR . 'src'))
                    {
                        $COMPONENTS_NS[$name] = rtrim($namespace, '\\');
                        break;
                    }
                }
            }
        }
    }
})();
