<?php

namespace Imi\Model\Relation\Struct;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Util\Imi;
use Imi\Util\Text;

trait TLeftAndRight
{
    /**
     * 左侧表字段.
     *
     * @var string
     */
    protected $leftField;

    /**
     * 右侧表字段.
     *
     * @var string
     */
    protected $rightField;

    /**
     * 右侧模型类.
     *
     * @var string
     */
    protected $rightModel;

    /**
     * 初始化左右关联.
     *
     * @param string                                      $className
     * @param string                                      $propertyName
     * @param \Imi\Model\Annotation\Relation\RelationBase $annotation
     *
     * @return void
     */
    public function initLeftAndRight($className, $propertyName, $annotation)
    {
        // @phpstan-ignore-next-line
        if (class_exists($annotation->model))
        {
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
     *
     * @return string
     */
    public function getLeftField()
    {
        return $this->leftField;
    }

    /**
     * Get the value of rightField.
     *
     * @return string
     */
    public function getRightField()
    {
        return $this->rightField;
    }

    /**
     * Get 右侧模型类.
     *
     * @return string
     */
    public function getRightModel()
    {
        return $this->rightModel;
    }
}
