<?php
namespace Imi\Aop;

/**
 * 切入点类型
 */
abstract class PointCutType
{
    /**
     * 方法
     */
    const METHOD = 1;

    /**
     * 带有注解的方法
     */
    const ANNOTATION = 2;
}