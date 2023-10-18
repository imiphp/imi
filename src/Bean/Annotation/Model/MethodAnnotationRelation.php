<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class MethodAnnotationRelation implements IAnnotationRelation
{
    public function __construct(
        /**
         * 类名.
         */
        private readonly string $class,
        /**
         * 方法名.
         */
        private readonly string $method,
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
     * Get 方法名.
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
