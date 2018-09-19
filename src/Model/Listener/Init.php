<?php
namespace Imi\Model\Listener;

use Imi\App;
use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Util\AtomicManager;
use Imi\Util\ChannelManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Model\Parser\ModelParser;
use Imi\Util\MemoryTableManager;

/**
 * @Listener(eventName="IMI.INITED",priority=PHP_INT_MAX)
 */
class Init implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // 初始化 MemoryTable
        $data = ModelParser::getInstance()->getData();
        foreach($data as $className => $classOption)
        {
            if(isset($classOption['MemoryTable']))
            {
                MemoryTableManager::addName($classOption['MemoryTable']->name, [
                    'size'    =>    $classOption['MemoryTable']->size,
                    'conflictProportion'    =>    $classOption['MemoryTable']->conflictProportion,
                    'columns'    =>    $this->getMemoryTableColumns($classOption),
                ]);
            }
        }
        MemoryTableManager::init();
    }

    protected function getMemoryTableColumns($classOption)
    {
        $columns = [];
        foreach($classOption['properties'] as $name => $propOption)
        {
            if(isset($propOption['Column']))
            {
                list($type, $size) = $this->parseColumnTypeAndSize($propOption['Column']);
                $columns[] = [
                    'name'    =>    $propOption['Column']->name,
                    'type'    =>    $type,
                    'size'    =>    $size,
                ];
            }
        }
        return $columns;
    }

    protected function parseColumnTypeAndSize($column)
    {
        $type = $column->type;
        switch($type)
        {
            case 'string':
                $type = \Swoole\Table::TYPE_STRING;
                $size = $column->length;
                break;
            case 'int':
                $type = \Swoole\Table::TYPE_INT;
                $size = $column->length;
                if(!in_array($size, [1, 2, 4, 8]))
                {
                    $size = 4;
                }
                break;
            case 'float':
                $type = \Swoole\Table::TYPE_FLOAT;
                $size = 8;
                break;
        }
        return [$type, $size];
    }

}