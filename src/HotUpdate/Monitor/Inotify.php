<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;
use Imi\Util\Imi;

class Inotify extends BaseMonitor
{
    /**
     * 目录们.
     */
    private array $paths = [];

    /**
     * inotify_init() 返回值
     *
     * @var resource
     */
    private $handler;

    /**
     * inotify_add_watch() mask参数.
     */
    protected int $mask = \IN_MODIFY | \IN_MOVE | \IN_CREATE | \IN_DELETE;

    /**
     * 更改的文件们.
     *
     * @var string[]
     */
    private array $changedFiles = [];

    /**
     * 排除规则.
     */
    private string $excludeRule = '';

    /**
     * 初始化.
     */
    protected function init(): void
    {
        if (!\extension_loaded('inotify'))
        {
            throw new \RuntimeException('The extension inotify is not installed');
        }
        $this->handler = $handler = inotify_init();
        stream_set_blocking($handler, false);

        $excludePaths = array_map(function ($item) {
            return Imi::parseRule($item);
        }, $this->excludePaths);

        $this->excludeRule = $excludeRule = '/^(?!((' . implode(')|(', $excludePaths) . ')))/';
        $paths = &$this->paths;
        $mask = &$this->mask;
        $includePaths = $this->includePaths;
        if ($includePaths)
        {
            foreach ($includePaths as $path)
            {
                if (is_file($path) && !is_dir($path))
                {
                    continue;
                }
                $paths[$path] ??= inotify_add_watch($handler, $path, $mask);
                foreach (File::enumFile($path) as $file)
                {
                    $fullPath = $file->getFullPath();
                    if (!is_dir($fullPath))
                    {
                        continue;
                    }
                    if ('' !== $excludeRule && !preg_match($excludeRule, $fullPath))
                    {
                        $file->setContinue(false);
                        continue;
                    }
                    $paths[$fullPath] ??= inotify_add_watch($handler, $fullPath, $mask);
                }
            }
        }
    }

    /**
     * 检测文件是否有更改.
     */
    public function isChanged(): bool
    {
        $changedFiles = &$this->changedFiles;
        $changedFiles = [];
        $paths = &$this->paths;
        $handler = &$this->handler;
        $mask = &$this->mask;
        $excludeRule = $this->excludeRule;
        while (true)
        {
            /** @var array|false $readResult */
            $readResult = inotify_read($handler);
            if (false === $readResult)
            {
                return isset($changedFiles[0]);
            }
            foreach ($readResult as $item)
            {
                $key = array_search($item['wd'], $paths);
                if (false === $key)
                {
                    continue;
                }
                $filePath = File::path($key, $item['name']);
                $filePathIsDir = is_dir($filePath);
                if (!$filePathIsDir && ('' === $excludeRule || preg_match($excludeRule, $filePath)))
                {
                    $changedFiles[] = $filePath;
                }
            }
        }
    }

    /**
     * 获取变更的文件们.
     */
    public function getChangedFiles(): array
    {
        return array_values(array_unique($this->changedFiles));
    }
}
