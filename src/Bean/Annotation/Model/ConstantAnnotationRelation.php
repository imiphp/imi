<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class ConstantAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private string $class;

    /**
     * 常量名.
     *
     * @var string
     */
    private string $constant;

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
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
     * Get 常量名.
     *
     * @return string
     */
    public function getConstant(): string
    {
        return $this->constant;
    }
}
