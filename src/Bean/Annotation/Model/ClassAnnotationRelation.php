<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class ClassAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private string $class = '';

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private Base $annotation;

    public function __construct(string $class, Base $annotation)
    {
        $this->class = $class;
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
}
