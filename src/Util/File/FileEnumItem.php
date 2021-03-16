<?php

declare(strict_types=1);

namespace Imi\Util\File;

use Imi\Util\File;

class FileEnumItem
{
    /**
     * 路径.
     */
    private string $path = '';

    /**
     * 文件名.
     */
    private string $fileName = '';

    /**
     * 完整路径.
     */
    private string $fullPath = '';

    /**
     * 如果是目录，是否继续向下遍历.
     */
    private bool $continue = true;

    public function __construct(string $path, string $fileName)
    {
        $this->path = $path;
        $this->fileName = $fileName;
        $this->fullPath = File::path($path, $fileName);
    }

    /**
     * Get 路径.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get 文件名.
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * 获取完整路径.
     */
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function __toString(): string
    {
        return $this->fullPath;
    }

    /**
     * Get 如果是目录，是否继续向下遍历.
     */
    public function getContinue(): bool
    {
        return $this->continue;
    }

    /**
     * Set 如果是目录，是否继续向下遍历.
     *
     * @param bool $continue 如果是目录，是否继续向下遍历
     */
    public function setContinue(bool $continue): self
    {
        $this->continue = $continue;

        return $this;
    }
}
