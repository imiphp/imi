<?php
namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;
use Imi\Util\Bit;

class Inotify extends BaseMonitor
{
    /**
     * 目录们
     *
     * @var array
     */
    private $paths = [];

    /**
     * inotify_init() 返回值
     * @var resource
     */
    private $handler;

    /**
     * inotify_add_watch() mask参数
     * @var int
     */
    protected $mask = IN_MODIFY | IN_MOVE | IN_CREATE | IN_DELETE;

    /**
     * 更改的文件们
     *
     * @var string[]
     */
    private $changedFiles = [];

    /**
     * 初始化
     * @return void
     */
    protected function init()
    {
        if(!\extension_loaded('inotify'))
        {
            throw new \RuntimeException('the extension inotify is not installed');
        }
        $this->handler = \inotify_init();
        stream_set_blocking($this->handler, 0);

        $this->excludeRule = implode('|', array_map('\Imi\Util\Imi::parseRule', $this->excludePaths));
        foreach($this->includePaths as $path)
        {
            \inotify_add_watch($this->handler, $path, $this->mask);
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO);
            $iterator = new \RecursiveIteratorIterator($directory);
            if('' === $this->excludeRule)
            {
                foreach($iterator as $fileName => $fileInfo)
                {
                    $filePath = dirname($fileName);
                    if(!isset($this->paths[$filePath]))
                    {
                        $this->paths[$filePath] = \inotify_add_watch($this->handler, $filePath, $this->mask);
                    }
                }
            }
            else
            {
                $rule = "/^(?!{$this->excludeRule}).+$/i";
                $regex = new \RegexIterator($iterator, $rule, \RecursiveRegexIterator::GET_MATCH);
                foreach ($regex as $item)
                {
                    $filePath = dirname($item[0]);
                    if(!isset($this->paths[$filePath]))
                    {
                        $this->paths[$filePath] = \inotify_add_watch($this->handler, $filePath, $this->mask);
                    }
                }
            }
        }
    }

    /**
     * 检测文件是否有更改
     * @return boolean
     */
    public function isChanged(): bool
    {
        $this->changedFiles = [];
        $result = null;
        do{
            $readResult = \inotify_read($this->handler);
            if(false === $readResult)
            {
                return $result ?? false;
            }
            foreach($readResult as $item)
            {
                $key = array_search($item['wd'], $this->paths);
                if(false === $key)
                {
                    continue;
                }
                $filePath = File::path($key, $item['name']);
                $filePathIsDir = is_dir($filePath);
                if(!$filePathIsDir)
                {
                    $this->changedFiles[] = $filePath;
                }
                if((Bit::has($item['mask'], IN_CREATE) || Bit::has($item['mask'], IN_MOVED_TO)) && $filePathIsDir && !$this->isExclude($filePath))
                {
                    $this->paths[$filePath] = \inotify_add_watch($this->handler, $filePath, $this->mask);
                }
            }
            $result = isset($readResult[0]);
        }while(true);
    }

    /**
     * 获取变更的文件们
     *
     * @return array
     */
    public function getChangedFiles(): array
    {
        return array_values(array_unique($this->changedFiles));
    }

    /**
     * 判断路径是否被排除
     * @param string $filePath
     * @return boolean
     */
    protected function isExclude($filePath)
    {
        return preg_match("/^(?!{$this->excludeRule}).+$/i", $filePath) > 0;
    }
}