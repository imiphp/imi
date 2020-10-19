<?php

namespace Imi\HotUpdate\Monitor;

interface IMonitor
{
    /**
     * 构造方法.
     *
     * @param array $includePaths 包含的路径
     * @param array $excludePaths 排除的路径
     */
    public function __construct(array $includePaths, array $excludePaths = []);

    /**
     * 检测文件是否有更改.
     *
     * @return bool
     */
    public function isChanged(): bool;

    /**
     * 获取变更的文件们.
     *
     * @return array
     */
    public function getChangedFiles(): array;
}
