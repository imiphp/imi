<?php

namespace Imi\Util\File;

use Imi\Util\File;

class FileEnumItem
{
    /**
     * 路径.
     *
     * @var string
     */
    private $path;

    /**
     * 文件名.
     *
     * @var string
     */
    private $fileName;

    /**
     * 完整路径.
     *
     * @var string
     */
    private $fullPath;

    /**
     * 如果是目录，是否继续向下遍历.
     *
     * @var bool
     */
    private $continue = true;

    public function __construct($path, $fileName)
    {
        $this->path = $path;
        $this->fileName = $fileName;
        $this->fullPath = File::path($path, $fileName);
    }

    /**
     * Get 路径.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get 文件名.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * 获取完整路径.
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    public function __toString()
    {
        return $this->fullPath;
    }

    /**
     * Get 如果是目录，是否继续向下遍历.
     *
     * @return bool
     */
    public function getContinue()
    {
        return $this->continue;
    }

    /**
     * Set 如果是目录，是否继续向下遍历.
     *
     * @param bool $continue 如果是目录，是否继续向下遍历
     *
     * @return self
     */
    public function setContinue(bool $continue)
    {
        $this->continue = $continue;

        return $this;
    }
}
