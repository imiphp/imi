<?php

declare(strict_types=1);

(function () {
    $componentsDir = dirname(__DIR__) . '/src/Components';
    foreach (new FilesystemIterator($componentsDir, FilesystemIterator::SKIP_DOTS) as $dir)
    {
        if (!$dir->isDir())
        {
            continue;
        }
        $fileName = $dir->getPathname() . '/vendor/autoload.php';
        if (is_file($fileName))
        {
            require_once $fileName;
        }
    }
})();
