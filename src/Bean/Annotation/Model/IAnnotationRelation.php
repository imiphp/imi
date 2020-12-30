<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

interface IAnnotationRelation
{
    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClass(): string;

    /**
     * Get 注解.
     *
     * @return \Imi\Bean\Annotation\Base
     */
    public function getAnnotation(): Base;
}
