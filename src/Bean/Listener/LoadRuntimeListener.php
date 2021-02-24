<?php

declare(strict_types=1);

namespace Imi\Bean\Listener;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanManager;
use Imi\Bean\PartialManager;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\File;

class LoadRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['bean'] ?? true))
        {
            return;
        }
        $eventData = $e->getData();
        ['fileName' => $fileName] = $eventData;
        $fileName = File::path($fileName, 'bean.cache');
        if (!$fileName || !is_file($fileName))
        {
            $eventData['success'] = false;
            $e->stopPropagation();

            return;
        }
        $data = unserialize(file_get_contents($fileName));
        if (!$data)
        {
            $eventData['success'] = false;
            $e->stopPropagation();

            return;
        }
        $parser = Annotation::getInstance()->getParser();
        if ($config['annotation_parser_data'] ?? true && isset($data['annotationParserData']))
        {
            $parser->loadStoreData($data['annotationParserData']);
        }
        if ($config['annotation_parser_parsers'] ?? true && isset($data['annotationParserParsers']))
        {
            $parser->setParsers($data['annotationParserParsers']);
        }
        if ($config['annotation_manager_annotations'] ?? true && isset($data['annotationManagerAnnotations']))
        {
            AnnotationManager::setAnnotations($data['annotationManagerAnnotations']);
        }
        if ($config['annotation_manager_annotation_relation'] ?? true && isset($data['annotationManagerAnnotationRelation']))
        {
            AnnotationManager::setAnnotationRelation($data['annotationManagerAnnotationRelation']);
        }
        if ($config['partial'] ?? true && isset($data['partial']))
        {
            PartialManager::setMap($data['partial']);
        }
        if ($config['bean'] ?? true && isset($data['bean']))
        {
            BeanManager::setMap($data['bean']);
        }
    }
}
