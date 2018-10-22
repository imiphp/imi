<?php
namespace Imi\Tool\Tools\Imi;

use Imi\App;
use Imi\Util\File;
use Imi\ServerManage;
use Imi\Tool\ArgType;
use Swoole\Coroutine;
use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Model\Annotation\Column;
use Imi\Tool\Annotation\Operation;
use Imi\Model\Annotation\MemoryTable;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @Tool("imi")
 */
class Imi
{
    /**
     * 开启服务
     * 
     * @Operation("getPreloadData")
     * 
     * @return void
     */
    public function getPreloadData()
    {
        App::initWorker();
        $annotationsSet = AnnotationManager::getAnnotationPoints(MemoryTable::class, 'Class');
        $memoryTableColumns = [];
        foreach($annotationsSet as &$item)
        {
            $item['columns'] = $this->getMemoryTableColumns(AnnotationManager::getPropertiesAnnotations($item['class'], Column::class)) ?? [];
        }
        echo json_encode([
            'MemoryTable'    =>  $annotationsSet,
        ]);
    }

    /**
     * 获取内存表列
     *
     * @param array $columnAnnotationsSet
     * @return array
     */
    protected function getMemoryTableColumns($columnAnnotationsSet)
    {
        $columns = [];

        foreach($columnAnnotationsSet as $propertyName => $annotations)
        {
            $columnAnnotation = $annotations[0];
            list($type, $size) = $this->parseColumnTypeAndSize($columnAnnotation);
            $columns[] = [
                'name' => $columnAnnotation->name,
                'type' => $type,
                'size' => $size,
            ];
        }
        
        return $columns;
    }

    /**
     * 处理列类型和大小
     *
     * @param \Imi\Model\Annotation\Column $column
     * @return [$type, $size]
     */
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