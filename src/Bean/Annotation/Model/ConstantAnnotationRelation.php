<?php

namespace Imi\Bean\Annotation\Model;

class ConstantAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private $class;

    /**
     * 常量名.
     *
     * @var string
     */
    private $constant;

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private $annotation;

    public function __construct(string $class, string $constant, \Imi\Bean\Annotation\Base $annotation)
    {
        $this->class = $class;
        $this->constant = $constant;
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
     * Get 常量名.
     *
     * @return string
     */
    public function getConstant()
    {
        return $this->constant;
    }
}
