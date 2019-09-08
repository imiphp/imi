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
                continue;
            }
            $this->excludePaths[$i] .= '/';
        }
        foreach($this->includePaths as $i => $path)
        {
            if(!$this->includePaths[$i] = $path = realpath($path))
            {
                unset($this->includePaths[$i]);
                continue;
            }
            foreach(File::enumFile($path) as $file)
            {
                $fullPath = $file->getFullPath();
                foreach($this->excludePaths as $path)
                {
                    if(substr($fullPath, 0, strlen($path)) === $path)
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
     * 处理初始化文件
     *
     * @param string $fileName
     * @return void
     */
    protected function parseInitFile($fileName)
    {
        if(is_file($fileName))
        {
            $this->files[$fileName] = [
                'exists'    => true,
                'mtime'        => filemtime($fileName),
            ];
        }
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
            foreach(File::enumFile($path) as $file)
            {
                $fullPath = $file->getFullPath();
                foreach($this->excludePaths as $path)
                {
                    if(substr($fullPath, 0, strlen($path)) === $path)
                    {
                        $file->setContinue(false);
                        continue 2;
                    }
                }
                if($this->parseCheckFile($fullPath))
                {
                    $this->changedFiles[] = $fullPath;
                    $changed = true;
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
        if(isset($this->files[$fileName]) || $isFile)
        {
            $this->files[$fileName] = [
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