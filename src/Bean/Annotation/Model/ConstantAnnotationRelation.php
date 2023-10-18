<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class ConstantAnnotationRelation implements IAnnotationRelation
{
    public function __construct(
        /**
         * 类名.
         */
        private readonly string $class,
        /**
         * 常量名.
         */
        private readonly string $constant,
        /**
         * 注解.
         */
        private readonly ?Base $annotation
    )
    {
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
     * Get 常量名.
     */
    public function getConstant(): string
    {
        return $this->constant;
    }
}
