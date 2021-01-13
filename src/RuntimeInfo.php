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
     *
     * @var array
     */
    public array $annotationParserData = [];

    /**
     * AnnotationParser->$parsers.
     *
     * @var array
     */
    public array $annotationParserParsers = [];

    /**
     * AnnotationManager->$annotations.
     *
     * @var array
     */
    public array $annotationManagerAnnotations = [];

    /**
     * AnnotationManager->$annotationRelation.
     *
     * @var AnnotationRelation
     */
    public AnnotationRelation $annotationManagerAnnotationRelation;

    /**
     * 处理器们的数据.
     *
     * @var array
     */
    public array $parsersData = [];
}
