<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;
use Imi\Util\Imi;

class FileMTime extends BaseMonitor
{
    /**
     * 文件记录集合.
     */
    private array $files = [];

    /**
     * 更改的文件们.
     */
    private array $changedFiles = [];

    /**
     * 排除规则.
     */
    private string $excludeRule = '';

    /**
     * {@inheritDoc}
     */
    protected function init(): void
    {
        $includePaths = &$this->includePaths;

        $excludePaths = array_map(function ($item) {
            return Imi::parseRule($item);
        }, $this->excludePaths);

        $this->excludeRule = $excludeRule = '/^(?!((' . implode(')|(', $excludePaths) . ')))/';

        foreach ($includePaths as $path)
        {
            if ($enumResult = File::enumFile($path))
            {
                foreach ($enumResult as $file)
                {
                    $fullPath = $file->getFullPath();
                    if ('' !== $excludeRule && !preg_match($excludeRule, $fullPath))
                    {
                        $file->setContinue(false);
                        continue;
                    }
                    $this->parseInitFile($fullPath);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function parseInitFile(string $fileName): void
    {
        if (is_file($fileName))
        {
            $this->files[$fileName] = [
                'exists'       => true,
                'mtime'        => filemtime($fileName),
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isChanged(): bool
    {
        $changed = false;
        $files = &$this->files;
        $files = array_map(function (array $item): array {
            $item['exists'] = false;

            return $item;
        }, $files);
        $changedFiles = &$this->changedFiles;
        $changedFiles = [];
        $includePaths = $this->includePaths;
        $excludeRule = $this->excludeRule;
        // 包含的路径中检测
        if ($includePaths)
        {
            foreach ($includePaths as $path)
            {
                if ($enumResult = File::enumFile($path))
                {
                    foreach ($enumResult as $file)
                    {
                        $fullPath = $file->getFullPath();
                        if ('' !== $excludeRule && !preg_match($excludeRule, $fullPath))
                        {
                            $file->setContinue(false);
                            continue;
                        }
                        if ($this->parseCheckFile($fullPath))
                        {
                            $changedFiles[] = $fullPath;
                            $changed = true;
                        }
                    }
                }
            }
        }
        // 之前有的文件被删处理
        foreach ($files as $fileName => $option)
        {
            if (!$option['exists'])
            {
                unset($files[$fileName]);
                $changedFiles[] = $fileName;
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * {@inheritDoc}
     */
    public function getChangedFiles(): array
    {
        return $this->changedFiles;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseCheckFile(string $fileName): bool
    {
        $files = &$this->files;
        $isFile = is_file($fileName);
        if ($isFile)
        {
            $changed = false;
            $mtime = filemtime($fileName);
            if (isset($files[$fileName]))
            {
                // 判断文件修改时间
                if ($files[$fileName]['mtime'] !== $mtime)
                {
                    $changed = true;
                }
            }
            else
            {
                $changed = true;
            }
        }
        else
        {
            $changed = true;
            $mtime = 0;
        }
        if (isset($files[$fileName]) || $isFile)
        {
            $files[$fileName] = [
                'exists'    => $isFile,
                'mtime'     => $mtime,
            ];

            return $changed;
        }
        else
        {
            return false;
        }
    }
}
