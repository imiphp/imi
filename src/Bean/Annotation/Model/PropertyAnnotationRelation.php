<?php

namespace Imi\Bean\Annotation\Model;

class PropertyAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private $class;

    /**
     * 属性名.
     *
     * @var string
     */
    private $property;

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private $annotation;

    public function __construct(string $class, string $property, \Imi\Bean\Annotation\Base $annotation)
    {
        $this->class = $class;
        $this->property = $property;
        $this->annotation = $annotation;
    }

    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get 注解.
     *
     * @return \Imi\Bean\Annotation\Base
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * Get 属性名.
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
