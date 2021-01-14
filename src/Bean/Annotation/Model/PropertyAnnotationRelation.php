<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

class PropertyAnnotationRelation implements IAnnotationRelation
{
    /**
     * 类名.
     *
     * @var string
     */
    private string $class = '';

    /**
     * 属性名.
     *
     * @var string
     */
    private string $property = '';

    /**
     * 注解.
     *
     * @var \Imi\Bean\Annotation\Base
     */
    private Base $annotation;

    public function __construct(string $class, string $property, Base $annotation)
    {
        $this->class = $class;
        $this->property = $property;
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
     * Get 属性名.
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }
}
