<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class MethodAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     */
    private string $class = '';

    /**
     * 方法名.
     */
    private string $method = '';

    /**
     * 注解.
     */
    private ?Base $annotation = null;

    public function __construct(string $class, string $method, Base $annotation)
    {
        $this->class = $class;
        $this->method = $method;
        $this->annotation = $annotation;
    }

    /**
     * {@inheritDoc}
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function getAnnotation(): Base
    {
        return $this->annotation;
    }

    /**
     * Get 方法名.
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
