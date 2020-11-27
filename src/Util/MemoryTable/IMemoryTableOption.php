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
     * @param array|null $option
     *
     * @return array
     */
    public function getOption(?array $option = null): array;
}
