<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Monitor;

abstract class BaseMonitor implements IMonitor
{
    /**
     * 构造方法.
     *
     * @param array $includePaths 包含的路径
     * @param array $excludePaths 排除的路径
     */
    public function __construct(protected array $includePaths, protected array $excludePaths = [])
    {
        $this->init();
    }

    /**
     * 初始化.
     */
    abstract protected function init(): void;
}
