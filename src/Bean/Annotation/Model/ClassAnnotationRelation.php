<?php

namespace Imi\Bean\Annotation\Model;

class ClassAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private $class;

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private $annotation;

    public function __construct(string $class, \Imi\Bean\Annotation\Base $annotation)
    {
        $this->class = $class;
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
}
