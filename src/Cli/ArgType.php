<?php

declare(strict_types=1);

namespace Imi\Cli;

/**
 * 参数类型.
 */
class ArgType
{
    public const STRING = 'string';

    public const INT = 'int';

    public const FLOAT = 'float';

    public const DOUBLE = 'float';

    public const BOOL = self::BOOLEAN;

    public const BOOLEAN = 'boolean';

    public const BOOL_NEGATABLE = self::BOOLEAN_NEGATABLE;

    public const BOOLEAN_NEGATABLE = 'boolean_negatable';

    public const ARRAY = 'array';

    public const ARRAY_EX = 'array_ex';

    public const MIXED = 'mixed';

    private function __construct()
    {
    }

    public static function isBooleanType(string $type): bool
    {
        return self::BOOLEAN === $type || self::BOOLEAN_NEGATABLE === $type || self::MIXED === $type;
    }
}
