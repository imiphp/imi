<?php

declare(strict_types=1);

namespace Imi\Cli;

/**
 * 命令行管理器.
 */
class CliManager
{
    private static array $map = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map)
    {
        self::$map = $map;
    }

    /**
     * 增加命令行定义.
     *
     * @param string|null $commandName
     * @param string      $actionName
     * @param string      $className
     * @param string      $methodName
     * @param bool        $dynamicOptions
     *
     * @return void
     */
    public static function addCommand(?string $commandName, string $actionName, string $className, string $methodName, bool $dynamicOptions = false)
    {
        self::$map['commands'][] = [
            'commandName'    => $commandName,
            'actionName'     => $actionName,
            'className'      => $className,
            'methodName'     => $methodName,
            'dynamicOptions' => $dynamicOptions,
        ];
    }

    /**
     * 增加参数注解.
     *
     * @param string|null $commandName
     * @param string      $actionName
     * @param string      $argumentName
     * @param string|null $type
     * @param mixed       $default
     * @param bool        $required
     * @param string      $comments
     *
     * @return void
     */
    public static function addArgument(?string $commandName, string $actionName, string $argumentName, ?string $type = null, $default = null, bool $required = false, string $comments = '')
    {
        self::$map['arguments'][$commandName][$actionName][$argumentName] = [
            'argumentName' => $argumentName,
            'type'         => $type,
            'default'      => $default,
            'required'     => $required,
            'comments'     => $comments,
        ];
    }

    /**
     * 增加可选参数注解.
     *
     * @param string|null $commandName
     * @param string      $actionName
     * @param string      $optionName
     * @param string|null $shortcut
     * @param string|null $type
     * @param mixed       $default
     * @param bool        $required
     * @param string      $comments
     *
     * @return void
     */
    public static function addOption(?string $commandName, string $actionName, string $optionName, ?string $shortcut = null, ?string $type = null, $default = null, bool $required = false, string $comments = '')
    {
        self::$map['options'][$commandName][$actionName][$optionName] = [
            'optionName' => $optionName,
            'shortcut'   => $shortcut,
            'type'       => $type,
            'default'    => $default,
            'required'   => $required,
            'comments'   => $comments,
        ];
    }

    /**
     * 获取所有命令.
     *
     * @return array
     */
    public static function getCommands(): array
    {
        return self::$map['commands'] ?? [];
    }

    /**
     * 获取命令参数列表.
     *
     * @param string $commandName
     * @param string $actionName
     *
     * @return array
     */
    public static function getArguments(string $commandName, string $actionName): array
    {
        return self::$map['arguments'][$commandName][$actionName] ?? [];
    }

    /**
     * 获取命令可选参数列表.
     *
     * @param string $commandName
     * @param string $actionName
     *
     * @return array
     */
    public static function getOptions(string $commandName, string $actionName): array
    {
        return self::$map['options'][$commandName][$actionName] ?? [];
    }
}
