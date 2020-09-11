<?php

namespace Imi\Util;

/**
 * 优先级定义
 * 19940312 代表的是宇润的生日.
 */
abstract class ImiPriority
{
    /**
     * 优先级最小值
     */
    const MIN = \PHP_INT_MIN;

    /**
     * 优先级最大值
     */
    const MAX = \PHP_INT_MAX;

    /**
     * 框架用到的最小优先级
     * 小于这个值，可以后于框架最小优先级执行.
     */
    const IMI_MIN = -19940312;

    /**
     * 框架用到的最大优先级
     * 大于这个值，可以先于框架最大优先级执行.
     */
    const IMI_MAX = 19940312;
}
