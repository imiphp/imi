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

class LoadRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e): void
    {
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['bean'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['bean'] ?? [];
        $parser = Annotation::getInstance()->getParser();
        if (($config['annotation_parser_data'] ?? true) && isset($data['annotationParserData']))
        {
            $parser->loadStoreData($data['annotationParserData']);
        }
        if (($config['annotation_parser_parsers'] ?? true) && isset($data['annotationParserParsers']))
        {
            $parser->setParsers($data['annotationParserParsers']);
        }
        if (($config['annotation_manager_annotations'] ?? true) && isset($data['annotationManagerAnnotations']))
        {
            AnnotationManager::setAnnotations($data['annotationManagerAnnotations']);
        }
        if (($config['annotation_manager_annotation_relation'] ?? true) && isset($data['annotationManagerAnnotationRelation']))
        {
            AnnotationManager::setAnnotationRelation($data['annotationManagerAnnotationRelation']);
        }
        if (($config['partial'] ?? true) && isset($data['partial']))
        {
            PartialManager::setMap($data['partial']);
        }
        // @phpstan-ignore-next-line
        if (($config['bean'] ?? true) && isset($data['bean']))
        {
            BeanManager::setMap($data['bean']);
        }
    }
}
