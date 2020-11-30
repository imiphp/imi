<?php

declare(strict_types=1);

namespace Imi\Cli;

class Tool
{
    private static $toolName;
    private static $toolOperation;

    private function __construct()
    {
    }

    /**
     * 获取当前命令行工具名称.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getToolName()
    {
        return static::$toolName;
    }

    /**
     * 获取当前命令行工具操作名称.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getToolOperation()
    {
        return static::$toolOperation;
    }
}
