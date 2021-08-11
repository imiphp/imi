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

    public const BOOL = 'boolean';

    public const BOOLEAN = 'boolean';

    public const ARRAY = 'array';

    public const ARRAY_EX = 'array_ex';

    private function __construct()
    {
    }
}
