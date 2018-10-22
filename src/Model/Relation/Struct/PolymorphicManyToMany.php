<?php
namespace Imi\Model\Relation\Struct;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Model\ModelManager;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\JoinToMiddle;
use Imi\Model\Annotation\Relation\JoinFromMiddle;

class PolymorphicManyToMany
{
    /**
     * 左侧表字段
     *
     * @var string
     */
    private $leftField;

    /**
     * 右侧表字段
     *
     * @var string
     */
    private $rightField;

    /**
     * 右侧模型类
     *
     * @var string
     */
    private $rightModel;

    /**
     * 中间表与左表关联的字段
     *
     * @var string
     */
    private $middleLeftField;

    /**
     * 中间表与右表关联的字段
     *
     * @var string
     */
    private $middleRightField;

    /**
     * 中间表模型类
     *
     * @var string
     */
    private $middleModel;

    /**
     * 初始化多对多结构
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\ManyToMany $annotation
     * @return void
     */
    public function __construct($className, $propertyName, $annotation)
    {
        if(class_exists($annotation->model))
        {
            $this->rightModel = $annotation->model;
        }
        else
        {
            $this->rightModel = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $joinToMiddle =  AnnotationManager::getPropertyAnnotations($className, $propertyName, JoinToMiddle::class)[0] ?? null;
        if(!$joinToMiddle instanceof JoinToMiddle)
        {
            throw new \RuntimeException(sprintf('%s->%s has no @JoinToMiddle', $className, $propertyName));
        }

        $joinFromMiddle =  AnnotationManager::getPropertyAnnotations($className, $propertyName, JoinFromMiddle::class)[0] ?? null;
        if(!$joinFromMiddle instanceof JoinFromMiddle)
        {
            throw new \RuntimeException(sprintf('%s->%s has no @JoinFromMiddle', $className, $propertyName));
        }

        $this->leftField = $joinToMiddle->field;
        $this->middleLeftField = $joinToMiddle->middleField;

        $this->rightField = $joinFromMiddle->field;
        $this->middleRightField = $joinFromMiddle->middleField;

        if(class_exists($annotation->middle))
        {
            $this->middleModel = $annotation->middle;
        }
        else
        {
            $this->middleModel = Imi::getClassNamespace($className) . '\\' . $annotation->middle;
        }
    }

    /**
     * Get 左侧表字段
     *
     * @return  string
     */ 
    public function getLeftField()
    {
        return $this->leftField;
    }

    /**
     * Get 右侧表字段
     *
     * @return  string
     */ 
    public function getRightField()
    {
        return $this->rightField;
    }

    /**
     * Get 右侧模型类
     *
     * @return  string
     */ 
    public function getRightModel()
    {
        return $this->rightModel;
    }

    /**
     * Get 中间表与左表关联的字段
     *
     * @return  string
     */ 
    public function getMiddleLeftField()
    {
        return $this->middleLeftField;
    }

    /**
     * Get 中间表与右表关联的字段
     *
     * @return  string
     */ 
    public function getMiddleRightField()
    {
        return $this->middleRightField;
    }

    /**
     * Get 中间表模型类
     *
     * @return  string
     */ 
    public function getMiddleModel()
    {
        return $this->middleModel;
    }
}