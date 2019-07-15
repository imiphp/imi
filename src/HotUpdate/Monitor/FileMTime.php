<?php
namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;


class FileMTime extends BaseMonitor
{
    /**
     * 文件记录集合
     *
     * @var array
     */
    private $files = [];

    /**
     * 排除规则
     *
     * @var array
     */
    private $excludeRule;

    /**
     * 更改的文件们
     *
     * @var array
     */
    private $changedFiles = [];

    /**
     * 初始化
     * @return void
     */
    protected function init()
    {
        foreach($this->excludePaths as $i => $path)
        {
            if(!$this->excludePaths[$i] = realpath($path))
            {
                unset($this->excludePaths[$i]);
            }
        }
        $this->excludeRule = implode('|', array_map('\Imi\Util\Imi::parseRule', $this->excludePaths));
        foreach($this->includePaths as $i => $path)
        {
            if(!$this->includePaths[$i] = $path = realpath($path))
            {
                unset($this->includePaths[$i]);
                continue;
            }
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($directory);
            if('' === $this->excludeRule)
            {
                foreach($iterator as $fileName => $fileInfo)
                {
                    $this->parseInitFile($fileName);
                }
            }
            else
            {
                $rule = "/^(?!{$this->excludeRule}).+$/i";
                $regex = new \RegexIterator($iterator, $rule, \RecursiveRegexIterator::GET_MATCH);
                foreach ($regex as $item)
                {
                    $this->parseInitFile($item[0]);
                }
            }
        }
    }

    /**
     * 处理初始化文件
     *
     * @param string $fileName
     * @return void
     */
    protected function parseInitFile($fileName)
    {
        $this->files[$fileName] = [
            'exists'    => true,
            'mtime'        => filemtime($fileName),
        ];
    }

    /**
     * 检测文件是否有更改
     * @return boolean
     */
    public function isChanged(): bool
    {
        $changed = false;
        $this->files = array_map(function($item){
            $item['exists'] = false;
            return $item;
        }, $this->files);
        $this->changedFiles = [];
        // 包含的路径中检测
        foreach($this->includePaths as $path)
        {
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($directory);
            if('' === $this->excludeRule)
            {
                // 无排除规则处理
                foreach($iterator as $fileName => $fileInfo)
                {
                    if($this->parseCheckFile($fileName))
                    {
                        $this->changedFiles[] = $fileName;
                        $changed = true;
                    }
                }
            }
            else
            {
                // 有排除规则处理
                $rule = "/^(?!{$this->excludeRule}).+$/i";
                $regex = new \RegexIterator($iterator, $rule, \RecursiveRegexIterator::GET_MATCH);
                foreach ($regex as $item)
                {
                    if($this->parseCheckFile($item[0]))
                    {
                        $this->changedFiles[] = $item[0];
                        $changed = true;
                    }
                }
            }
        }
        // 之前有的文件被删处理
        foreach($this->files as $fileName => $option)
        {
            if(!$option['exists'])
            {
                unset($this->files[$fileName]);
                $this->changedFiles[] = $fileName;
                $changed = true;
            }
        }
        return $changed;
    }

    /**
     * 获取变更的文件们
     *
     * @return array
     */
    public function getChangedFiles(): array
    {
        return $this->changedFiles;
    }

    /**
     * 处理检查文件是否更改，返回是否更改
     * @param string $fileName
     * @return bool
     */
    protected function parseCheckFile($fileName)
    {
        $isFile = is_file($fileName);
        if($isFile)
        {
            $changed = false;
            $mtime = filemtime($fileName);
            if(isset($this->files[$fileName]))
            {
                // 判断文件修改时间
                if($this->files[$fileName]['mtime'] !== $mtime)
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
        $this->files[$fileName] = [
            'exists'    => $isFile,
            'mtime'     => $mtime,
        ];
        return $changed;
    }
    
}