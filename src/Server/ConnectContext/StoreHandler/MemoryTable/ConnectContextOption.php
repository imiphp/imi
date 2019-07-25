<?php
namespace Imi\Server\ConnectContext\StoreHandler\MemoryTable;

use Imi\Util\MemoryTable\IMemoryTableOption;

/**
 * Swoole 内存表
 */
class ConnectContextOption implements IMemoryTableOption
{
    /**
     * 获取配置
     *
     * @return array
     */
    public function getOption($option = null): array
    {
        if(!$option)
        {
            $option = [];
        }
        if(!isset($option['size']))
        {
            $option['size'] = 65536;
        }
        $option['columns'] = [
            ['name' => 'fd', 'type' => \Swoole\Table::TYPE_INT, 'size' => 4],
            ['name' => 'data', 'type' => \Swoole\Table::TYPE_STRING, 'size' => $option['dataLength'] ?? 1024],
        ];
        return $option;
    }

}
