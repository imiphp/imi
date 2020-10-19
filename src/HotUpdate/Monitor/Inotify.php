<?php

namespace Imi\HotUpdate\Monitor;

use Imi\Util\Bit;
use Imi\Util\File;

class Inotify extends BaseMonitor
{
    /**
     * 目录们.
     *
     * @var array
     */
    private $paths = [];

    /**
     * inotify_init() 返回值
     *
     * @var resource
     */
    private $handler;

    /**
     * inotify_add_watch() mask参数.
     *
     * @var int
     */
    protected $mask = IN_MODIFY | IN_MOVE | IN_CREATE | IN_DELETE;

    /**
     * 更改的文件们.
     *
     * @var string[]
     */
    private $changedFiles = [];

    /**
     * 初始化.
     *
     * @return void
     */
    protected function init()
    {
        if (!\extension_loaded('inotify'))
        {
            throw new \RuntimeException('the extension inotify is not installed');
        }
        $this->handler = $handler = inotify_init();
        stream_set_blocking($handler, 0);

        $excludePaths = &$this->excludePaths;
        $excludeRule = &$this->excludeRule;
        $excludeRule = implode('|', array_map('\Imi\Util\Imi::parseRule', $excludePaths));
        $paths = &$this->paths;
        $mask = &$this->mask;
        foreach ($this->includePaths as $path)
        {
            if (!is_dir($path))
            {
                continue;
            }
            inotify_add_watch($handler, $path, $mask);
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO);
            $iterator = new \RecursiveIteratorIterator($directory);
            if ('' === $excludeRule)
            {
                foreach ($iterator as $fileName => $fileInfo)
                {
                    $filePath = \dirname($fileName);
                    if (!isset($paths[$filePath]))
                    {
                        $paths[$filePath] = inotify_add_watch($handler, $filePath, $mask);
                    }
                }
            }
            else
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
                    $filePath = $file->getPath();
                    if (!isset($paths[$filePath]))
                    {
                        $paths[$filePath] = inotify_add_watch($handler, $filePath, $mask);
                    }
                }
            }
        }
    }

    /**
     * 检测文件是否有更改.
     *
     * @return bool
     */
    public function isChanged(): bool
    {
        $changedFiles = &$this->changedFiles;
        $changedFiles = [];
        $paths = &$this->paths;
        $handler = &$this->handler;
        $mask = &$this->mask;
        do
        {
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
                if (!$filePathIsDir)
                {
                    $changedFiles[] = $filePath;
                }
                if ((Bit::has($item['mask'], IN_CREATE) || Bit::has($item['mask'], IN_MOVED_TO)) && $filePathIsDir && !$this->isExclude($filePath))
                {
                    $paths[$filePath] = inotify_add_watch($handler, $filePath, $mask);
                }
            }
        } while (true);
    }

    /**
     * 获取变更的文件们.
     *
     * @return array
     */
    public function getChangedFiles(): array
    {
        return array_values(array_unique($this->changedFiles));
    }

    /**
     * 判断路径是否被排除.
     *
     * @param string $filePath
     *
     * @return bool
     */
    protected function isExclude($filePath)
    {
        return preg_match("/^(?!{$this->excludeRule}).+$/i", $filePath) > 0;
    }
}
