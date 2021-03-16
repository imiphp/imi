<?php

declare(strict_types=1);

namespace Imi;

use Imi\Bean\Annotation\Model\AnnotationRelation;

/**
 * 运行时数据.
 */
class RuntimeInfo
{
    /**
     * AnnotationParser->$data & AnnotationParser->$files.
     */
    public array $annotationParserData = [];

    /**
     * AnnotationParser->$parsers.
     */
    public array $annotationParserParsers = [];

    /**
     * AnnotationManager->$annotations.
     */
    public array $annotationManagerAnnotations = [];

    /**
     * AnnotationManager->$annotationRelation.
     */
    public AnnotationRelation $annotationManagerAnnotationRelation;

    /**
     * 处理器们的数据.
     */
    public array $parsersData = [];
}
