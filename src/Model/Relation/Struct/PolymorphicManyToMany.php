<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Struct;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\JoinFromMiddle;
use Imi\Model\Annotation\Relation\JoinToMiddle;
use Imi\Util\Imi;

class PolymorphicManyToMany
{
    /**
     * 左侧表字段.
     */
    private string $leftField = '';

    /**
     * 右侧表字段.
     */
    private string $rightField = '';

    /**
     * 右侧模型类.
     */
    private string $rightModel = '';

    /**
     * 中间表与左表关联的字段.
     */
    private string $middleLeftField = '';

    /**
     * 中间表与右表关联的字段.
     */
    private string $middleRightField = '';

    /**
     * 中间表模型类.
     */
    private string $middleModel = '';

    /**
     * 初始化多对多结构.
     *
     * @param \Imi\Model\Annotation\Relation\PolymorphicManyToMany|\Imi\Model\Annotation\Relation\PolymorphicToMany $annotation
     */
    public function __construct(string $className, string $propertyName, $annotation)
    {
        if (class_exists($annotation->model))
        {
            $this->rightModel = $annotation->model;
        }
        else
        {
            $this->rightModel = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $annotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
            JoinToMiddle::class,
            JoinFromMiddle::class,
        ], true, true);
        if ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
        {
            $joinToMiddle = $annotations[JoinToMiddle::class];
            if (!$joinToMiddle instanceof JoinToMiddle)
            {
                throw new \RuntimeException(sprintf('%s->%s has no @JoinToMiddle', $className, $propertyName));
            }

            $joinFromMiddle = $annotations[JoinFromMiddle::class];
            if (!$joinFromMiddle instanceof JoinFromMiddle)
            {
                throw new \RuntimeException(sprintf('%s->%s has no @JoinFromMiddle', $className, $propertyName));
            }

            $this->leftField = $joinToMiddle->field;
            $this->middleLeftField = $joinToMiddle->middleField;

            $this->rightField = $joinFromMiddle->field;
            $this->middleRightField = $joinFromMiddle->middleField;
        }
        else
        {
            /** @var JoinToMiddle|null $joinToMiddle */
            $joinToMiddle = $annotations[JoinToMiddle::class];

            /** @var JoinFromMiddle|null $joinFromMiddle */
            $joinFromMiddle = $annotations[JoinFromMiddle::class];

            $this->leftField = '' === $annotation->field ? $joinToMiddle->field : $annotation->field;
            $this->middleLeftField = '' === $annotation->middleLeftField ? $joinToMiddle->middleField : $annotation->middleLeftField;

            $this->rightField = '' === $annotation->modelField ? $joinFromMiddle->field : $annotation->modelField;
            $this->middleRightField = '' === $annotation->middleRightField ? $joinFromMiddle->middleField : $annotation->middleRightField;
        }

        if (class_exists($annotation->middle))
        {
            $this->middleModel = $annotation->middle;
        }
        else
        {
            $this->middleModel = Imi::getClassNamespace($className) . '\\' . $annotation->middle;
        }
    }

    /**
     * Get 左侧表字段.
     */
    public function getLeftField(): string
    {
        return $this->leftField;
    }

    /**
     * Get 右侧表字段.
     */
    public function getRightField(): string
    {
        return $this->rightField;
    }

    /**
     * Get 右侧模型类.
     */
    public function getRightModel(): string
    {
        return $this->rightModel;
    }

    /**
     * Get 中间表与左表关联的字段.
     */
    public function getMiddleLeftField(): string
    {
        return $this->middleLeftField;
    }

    /**
     * Get 中间表与右表关联的字段.
     */
    public function getMiddleRightField(): string
    {
        return $this->middleRightField;
    }

    /**
     * Get 中间表模型类.
     */
    public function getMiddleModel(): string
    {
        return $this->middleModel;
    }
}
