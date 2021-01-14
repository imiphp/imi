<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class MethodAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private string $class = '';

    /**
     * 方法名.
     *
     * @var string
     */
    private string $method = '';

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private Base $annotation;

    public function __construct(string $class, string $method, Base $annotation)
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
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Get 注解.
     *
     * @return \Imi\Bean\Annotation\Base
     */
    public function getAnnotation(): Base
    {
        return $this->annotation;
    }

    /**
     * Get 方法名.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
