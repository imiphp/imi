<?php

declare(strict_types=1);

namespace Imi\Util\MemoryTable;

/**
 * 内存表配置.
 */
interface IMemoryTableOption
{
    /**
     * 获取配置.
     */
    public function getOption(?array $option = null): array;
}
