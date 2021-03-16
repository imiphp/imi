<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class ConstantAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     */
    private string $class = '';

    /**
     * 常量名.
     */
    private string $constant = '';

    /**
     * 注解.
     */
    private Base $annotation;

    public function __construct(string $class, string $constant, Base $annotation)
    {
        $this->class = $class;
        $this->constant = $constant;
        $this->annotation = $annotation;
    }

    /**
     * Get 类名.
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Get 注解.
     */
    public function getAnnotation(): Base
    {
        return $this->annotation;
    }

    /**
     * Get 常量名.
     */
    public function getConstant(): string
    {
        return $this->constant;
    }
}
