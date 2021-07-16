<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Consts;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

class LogicalOperator extends BaseEnum
{
    /**
     * @EnumItem
     */
    public const AND = 'and';

    /**
     * @EnumItem
     */
    public const OR = 'or';

    /**
     * @EnumItem
     */
    public const XOR = 'xor';

    /**
     * @EnumItem
     */
    public const AND_NOT = 'and not';

    /**
     * @EnumItem
     */
    public const OR_NOT = 'or not';

    /**
     * @EnumItem
     */
    public const XOR_NOT = 'xor not';

    private function __construct()
    {
    }
}
