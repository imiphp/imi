<?php

declare(strict_types=1);

namespace Imi\Swoole\HotUpdate\Monitor;

abstract class BaseMonitor implements IMonitor
{
    /**
     * 包含的路径.
     *
     * @var array
     */
    protected array $includePaths = [];

    /**
     * 排除的路径.
     *
     * @var array
     */
    protected array $excludePaths = [];

    /**
     * 构造方法.
     *
     * @param array $includePaths 包含的路径
     * @param array $excludePaths 排除的路径
     */
    public function __construct(array $includePaths, array $excludePaths = [])
    {
        $this->includePaths = $includePaths;
        $this->excludePaths = $excludePaths;
        $this->init();
    }

    /**
     * 初始化.
     *
     * @return void
     */
    abstract protected function init();
}
