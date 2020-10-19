<?php

namespace Imi\Bean\Annotation\Model;

class MethodAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private $class;

    /**
     * 方法名.
     *
     * @var string
     */
    private $method;

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private $annotation;

    public function __construct(string $class, string $method, \Imi\Bean\Annotation\Base $annotation)
    {
        $this->class = $class;
        $this->method = $method;
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
     * Get 方法名.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
