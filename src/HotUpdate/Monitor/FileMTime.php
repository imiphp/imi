<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;
use Imi\Util\Imi;

class FileMTime extends BaseMonitor
{
    /**
     * 文件记录集合.
     *
     * @var array
     */
    private $files = [];

    /**
     * 更改的文件们.
     *
     * @var array
     */
    private $changedFiles = [];

    /**
     * 排除规则.
     *
     * @var string
     */
    private $excludeRule = '';

    /**
     * 初始化.
     *
     * @return void
     */
    protected function init()
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
