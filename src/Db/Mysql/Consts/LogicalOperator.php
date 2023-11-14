<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Consts;

class LogicalOperator
{
    use \Imi\Util\Traits\TStaticClass;

    public const AND = 'and';

    public const OR = 'or';

    public const XOR = 'xor';

    public const AND_NOT = 'and not';

    public const OR_NOT = 'or not';

    public const XOR_NOT = 'xor not';

    public static function values(): array
    {
        return [
            static::AND,
            static::OR,
            static::XOR,
            static::AND_NOT,
            static::OR_NOT,
            static::XOR_NOT,
        ];
    }
}
