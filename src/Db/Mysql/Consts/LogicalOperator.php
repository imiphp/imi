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
}
