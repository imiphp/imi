<?php

declare(strict_types=1);

namespace Imi\Aop;

/**
 * 切入点类型.
 */
class PointCutType
{
    /**
     * 方法.
     */
    public const METHOD = 1;

    /**
     * 带有注解的方法.
     */
    public const ANNOTATION = 2;

    /**
     * 构造方法.
     */
    public const CONSTRUCT = 3;

    /**
     * 带有注解的类的构造方法.
     */
    public const ANNOTATION_CONSTRUCT = 4;

    private function __construct()
    {
    }
}
