<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class ClassAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     */
    private string $class = '';

    /**
     * 注解.
     */
    private Base $annotation;

    public function __construct(string $class, Base $annotation)
    {
        $this->class = $class;
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
}
