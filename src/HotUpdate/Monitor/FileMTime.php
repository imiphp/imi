<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;

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
     * 初始化.
     */
    protected function init(): void
    {
        $excludePaths = &$this->excludePaths;
        $includePaths = &$this->includePaths;
        foreach ($excludePaths as $i => $path)
        {
            if (!$excludePaths[$i] = realpath($path))
            {
                unset($excludePaths[$i]);
                continue;
            }
            $excludePaths[$i] .= '/';
        }
        foreach ($includePaths as $i => $path)
        {
            if (!$includePaths[$i] = $path = realpath($path))
            {
                unset($includePaths[$i]);
                continue;
            }
            foreach (File::enumFile($path) as $file)
            {
                $fullPath = $file->getFullPath();
                foreach ($excludePaths as $path)
                {
                    if (substr($fullPath, 0, \strlen($path)) === $path)
                    {
                        $file->setContinue(false);
                        continue 2;
                    }
                }
                $this->parseInitFile($fullPath);
            }
        }
    }

    /**
     * 处理初始化文件.
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
     * 检测文件是否有更改.
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
        $excludePaths = &$this->excludePaths;
        // 包含的路径中检测
        foreach ($this->includePaths as $path)
        {
            foreach (File::enumFile($path) as $file)
            {
                $fullPath = $file->getFullPath();
                foreach ($excludePaths as $path)
                {
                    if (substr($fullPath, 0, \strlen($path)) === $path)
                    {
                        $file->setContinue(false);
                        continue 2;
                    }
                }
                if ($this->parseCheckFile($fullPath))
                {
                    $changedFiles[] = $fullPath;
                    $changed = true;
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
     * 获取变更的文件们.
     */
    public function getChangedFiles(): array
    {
        return $this->changedFiles;
    }

    /**
     * 处理检查文件是否更改，返回是否更改.
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
