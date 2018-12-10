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
use Imi\Util\Args;

/**
 * @Tool("imi")
 */
class Imi
{
    /**
     * 构建框架预加载缓存
     * 
     * 构建后有助于提升性能
     * 
     * @Operation("buildImiRuntime")
     * 
     * @return void
     */
    public function buildImiRuntime()
    {
        $file = \Imi\Util\Imi::getRuntimePath('imi-runtime.cache');
        ImiUtil::buildRuntime(\Imi\Util\Imi::getRuntimePath('imi-runtime.cache'));
        echo 'Build imi runtime complete', PHP_EOL;
    }

    /**
     * 清除框架预加载缓存
     * 
     * @Operation("clearImiRuntime")
     * 
     * @return void
     */
    public function clearImiRuntime()
    {
        $file = \Imi\Util\Imi::getRuntimePath('imi-runtime.cache');
        if(is_file($file))
        {
            unlink($file);
            echo 'Clear imi runtime complete', PHP_EOL;
        }
        else
        {
            echo 'Imi runtime does not exists', PHP_EOL;
        }
    }

    /**
     * 构建项目预加载缓存
     * 
     * @Operation("buildRuntime")
     * 
     * @Arg(name="format", type=ArgType::STRING, default="", comments="返回数据格式，可选：json或其他。json格式框架启动、热重启构建缓存需要。")
     * @Arg(name="changedFilesFile", type=ArgType::STRING, default=null, comments="保存改变的文件列表的文件，一行一个")
     * 
     * @return void
     */
    public function buildRuntime($format, $changedFilesFile)
    {
        ob_start();
        register_shutdown_function(function() use($format){
            $result = ob_get_clean();
            if('' === $result)
            {
                $result = 'Build app runtime complete' . PHP_EOL;
            }
            if('json' === $format)
            {
                echo json_encode($result);
            }
            else
            {
                echo $result;
            }
        });
        
        if(null !== $changedFilesFile && App::loadRuntimeInfo(ImiUtil::getRuntimePath('runtime.cache')))
        {
            $files = explode("\n", file_get_contents($changedFilesFile));
            ImiUtil::incrUpdateRuntime($files);
        }
        else
        {
            // 加载服务器注解
            Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
        }
        ImiUtil::buildRuntime();
    }

    /**
     * 清除项目预加载缓存
     * 
     * @Operation("clearRuntime")
     * 
     * @return void
     */
    public function clearRuntime()
    {
        $file = \Imi\Util\Imi::getRuntimePath('runtime.cache');
        if(is_file($file))
        {
            unlink($file);
            echo 'Clear app runtime complete', PHP_EOL;
        }
        else
        {
            echo 'App runtime does not exists', PHP_EOL;
        }
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