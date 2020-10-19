<?php

namespace Imi;

/**
 * 运行时数据.
 */
class RuntimeInfo
{
    /**
     * Swoole Memory Table 配置.
     *
     * @var array
     */
    public $memoryTable = [];

    /**
     * AnnotationParser->$data & AnnotationParser->$files.
     *
     * @var array
     */
    public $annotationParserData = [];

    /**
     * AnnotationParser->$parsers.
     *
     * @var array
     */
    public $annotationParserParsers = [];

    /**
     * AnnotationManager->$annotations.
     *
     * @var array
     */
    public $annotationManagerAnnotations = [];

    /**
     * AnnotationManager->$annotationRelation.
     *
     * @var array
     */
    public $annotationManagerAnnotationRelation = [];

    /**
     * 处理器们的数据.
     *
     * @var array
     */
    public $parsersData = [];
}
