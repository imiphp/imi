<?php

namespace Imi\Bean\Annotation\Model;

interface IAnnotationRelation
{
    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClass();

    /**
     * Get 注解.
     *
     * @return \Imi\Bean\Annotation\Base
     */
    public function getAnnotation();
}
