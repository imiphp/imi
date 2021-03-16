<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Struct;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\RelationBase;
use Imi\Util\Imi;
use Imi\Util\Text;

trait TLeftAndRight
{
    /**
     * 左侧表字段.
     */
    protected string $leftField = '';

    /**
     * 右侧表字段.
     */
    protected string $rightField = '';

    /**
     * 右侧模型类.
     */
    protected string $rightModel = '';

    /**
     * 初始化左右关联.
     */
    public function initLeftAndRight(string $className, string $propertyName, RelationBase $annotation): void
    {
        // @phpstan-ignore-next-line
        if (class_exists($annotation->model))
        {
            // @phpstan-ignore-next-line
            $this->rightModel = $annotation->model;
        }
        else
        {
            // @phpstan-ignore-next-line
            $this->rightModel = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        /** @var JoinFrom|null $joinFrom */
        $joinFrom = AnnotationManager::getPropertyAnnotations($className, $propertyName, JoinFrom::class)[0] ?? null;
        /** @var JoinTo|null $joinTo */
        $joinTo = AnnotationManager::getPropertyAnnotations($className, $propertyName, JoinTo::class)[0] ?? null;

        if ($joinFrom)
        {
            $this->leftField = $joinFrom->field;
        }
        else
        {
            $this->leftField = $className::__getMeta()->getFirstId();
        }

        if ($joinTo)
        {
            $this->rightField = $joinTo->field;
        }
        else
        {
            $this->rightField = Text::toUnderScoreCase(Imi::getClassShortName($className)) . '_id';
        }
    }

    /**
     * Get the value of leftField.
     */
    public function getLeftField(): string
    {
        return $this->leftField;
    }

    /**
     * Get the value of rightField.
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
}
