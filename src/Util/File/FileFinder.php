<?php

declare(strict_types=1);

namespace Imi\Util\File;

use function array_filter;
use function array_values;
use function file_exists;
use function implode;
use function is_file;
use PHPStan\File\FileExcluder;
use PHPStan\File\FileFinderResult;
use PHPStan\File\FileHelper;
use PHPStan\File\PathNotFoundException;
use Symfony\Component\Finder\Finder;

class FileFinder extends \PHPStan\File\FileFinder
{
    /**
     * @param string[] $fileExtensions
     */
    public function __construct(
        private FileExcluder $fileExcluder,
        private FileHelper $fileHelper,
        private array $fileExtensions,
    ) {
    }

    /**
     * @param string[] $paths
     */
    public function findFiles(array $paths): FileFinderResult
    {
        $onlyFiles = true;
        $files = [];
        foreach ($paths as $path)
        {
            if (is_file($path))
            {
                $files[] = $this->fileHelper->normalizePath($path);
            }
            elseif (!file_exists($path))
            {
                throw new PathNotFoundException($path);
            }
            else
            {
                $finder = new Finder();
                // 此行注释，防止无限套娃
                // $finder->followLinks();
                foreach ($finder->files()->name('*.{' . implode(',', $this->fileExtensions) . '}')->in($path) as $fileInfo)
                {
                    $files[] = $this->fileHelper->normalizePath($fileInfo->getPathname());
                    $onlyFiles = false;
                }
            }
        }

        $files = array_values(array_filter($files, fn (string $file): bool => !$this->fileExcluder->isExcludedFromAnalysing($file)));

        return new FileFinderResult($files, $onlyFiles);
    }
}
