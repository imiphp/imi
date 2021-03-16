<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Base;

interface IAnnotationRelation
{
    /**
     * Get 类名.
     */
    public function getClass(): string;

    /**
     * Get 注解.
     */
    public function getAnnotation(): Base;
}
