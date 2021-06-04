<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\StoreHandler\MemoryTable;

use Imi\Util\MemoryTable\IMemoryTableOption;

/**
 * Swoole 内存表.
 */
class ConnectionContextOption implements IMemoryTableOption
{
    /**
     * 获取配置.
     */
    public function getOption(?array $option = null): array
    {
        if (!$option)
        {
            $option = [];
        }
        $option['size'] ??= 65536;
        $option['columns'] = [
            ['name' => 'data', 'type' => \Swoole\Table::TYPE_STRING, 'size' => $option['dataLength'] ?? 1024],
        ];

        return $option;
    }
}
