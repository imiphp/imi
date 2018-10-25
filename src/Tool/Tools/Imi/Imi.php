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
use Imi\Util\Imi as ImiUtil;
use Imi\Bean\Annotation;
use \Imi\Main\Helper as MainHelper;

/**
 * @Tool("imi")
 */
class Imi
{
    /**
     * 获取预加载信息
     * 
     * @Operation("buildRuntime")
     * 
     * @return void
     */
    public function buildRuntime()
    {
        // 加载服务器注解
        Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
        $runtimeInfo = App::getRuntimeInfo();
        $annotationsSet = AnnotationManager::getAnnotationPoints(MemoryTable::class, 'Class');
        foreach($annotationsSet as &$item)
        {
            $item['columns'] = $this->getMemoryTableColumns(AnnotationManager::getPropertiesAnnotations($item['class'], Column::class)) ?? [];
        }
        $runtimeInfo->memoryTable = $annotationsSet;
        $runtimeInfo->annotationParserData = Annotation::getInstance()->getParser()->getData();
        $runtimeInfo->annotationParserParsers = Annotation::getInstance()->getParser()->getParsers();
        $runtimeInfo->annotationManagerAnnotations = AnnotationManager::getAnnotations();
        $runtimeInfo->annotationManagerAnnotationRelation = AnnotationManager::getAnnotationRelation();
        $runtimeInfo->parsersData = [];
        foreach(array_unique($runtimeInfo->annotationParserParsers) as $parserClass)
        {
            $parser = $parserClass::getInstance();
            $runtimeInfo->parsersData[$parserClass] = $parser->getData();
        }
        file_put_contents(ImiUtil::getRuntimeFilePath(), \Swoole\Serialize::pack($runtimeInfo));
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