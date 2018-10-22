<?php
namespace Imi\Model\Listener;

use Imi\App;
use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Util\AtomicManager;
use Imi\Util\ChannelManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Util\MemoryTableManager;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\MemoryTable;
use Imi\Model\Annotation\Column;
use Imi\Tool\Tool;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.INITED",priority=1)
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
        if('server' !== Tool::getToolName() || 'start' !== Tool::getToolOperation())
        {
            return;
        }

        $result = exec(Imi::getImiCmd('imi', 'getPreloadData'));
        $set = json_decode($result, true);
        if(!$set)
        {
            return;
        }

        // 初始化 MemoryTable
        foreach($set['MemoryTable'] as $item)
        {
            $memoryTableAnnotation = new MemoryTable($item['annotation']);
            MemoryTableManager::addName($memoryTableAnnotation->name, [
                'size'                  => $memoryTableAnnotation->size,
                'conflictProportion'    => $memoryTableAnnotation->conflictProportion,
                'columns'               => $item['columns'],
            ]);
        }

        MemoryTableManager::init();
    }

}