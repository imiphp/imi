<?php

declare(strict_types=1);

namespace Imi\Cli;

/**
 * 命令行管理器.
 */
class CliManager
{
    private static array $map = [];

    private static array $commandActionMap = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map): void
    {
        self::$map = $map;
        foreach ($map['commands'] ?? [] as $item)
        {
            self::$commandActionMap[$item['commandName']][$item['actionName']] = true;
        }
    }

    /**
     * 增加命令行定义.
     */
    public static function addCommand(?string $commandName, string $actionName, string $className, string $methodName, bool $dynamicOptions = false, string $separator = '/'): void
    {
        if (isset(self::$commandActionMap[$commandName][$actionName]))
        {
            return;
        }
        self::$map['commands'][] = [
            'commandName'    => $commandName,
            'actionName'     => $actionName,
            'className'      => $className,
            'methodName'     => $methodName,
            'dynamicOptions' => $dynamicOptions,
            'separator'      => $separator,
        ];
        self::$commandActionMap[$commandName][$actionName] = true;
    }

    /**
     * 增加参数注解.
     *
     * @param mixed $default
     */
    public static function addArgument(?string $commandName, string $actionName, string $argumentName, ?string $type = null, $default = null, bool $required = false, string $comments = '', string $to = ''): void
    {
        self::$map['arguments'][$commandName][$actionName][$argumentName] = [
            'argumentName' => $argumentName,
            'type'         => $type,
            'default'      => $default,
            'required'     => $required,
            'comments'     => $comments,
            'to'           => $to,
        ];
    }

    /**
     * 增加可选参数注解.
     *
     * @param mixed $default
     */
    public static function addOption(?string $commandName, string $actionName, string $optionName, ?string $shortcut = null, ?string $type = null, $default = null, bool $required = false, string $comments = '', string $to = ''): void
    {
        self::$map['options'][$commandName][$actionName][$optionName] = [
            'optionName' => $optionName,
            'shortcut'   => $shortcut,
            'type'       => $type,
            'default'    => $default,
            'required'   => $required,
            'comments'   => $comments,
            'to'         => $to,
        ];
    }

    /**
     * 获取所有命令.
     */
    public static function getCommands(): array
    {
        return self::$map['commands'] ?? [];
    }

    /**
     * 获取命令参数列表.
     */
    public static function getArguments(string $commandName, string $actionName): array
    {
        return self::$map['arguments'][$commandName][$actionName] ?? [];
    }

    /**
     * 获取命令可选参数列表.
     */
    public static function getOptions(string $commandName, string $actionName): array
    {
        return self::$map['options'][$commandName][$actionName] ?? [];
    }
}
