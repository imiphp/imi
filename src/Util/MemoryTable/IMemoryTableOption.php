<?php

namespace Imi\Util\MemoryTable;

/**
 * 内存表配置.
 */
interface IMemoryTableOption
{
    /**
     * 获取配置.
     *
     * @param mixed $option
     *
     * @return array
     */
    public function getOption($option = null): array;
}
