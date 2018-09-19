<?php
namespace Imi\Db\Consts;

abstract class LogicalOperator
{
    const AND = 'and';

    const OR = 'or';

    const XOR = 'xor';

    const AND_NOT = 'and not';

    const OR_NOT = 'or not';

    const XOR_NOT = 'xor not';

    const ALL = 'all';

    const ANY = 'any';

    const BETWEEN = 'between';

    const EXISTS = 'exists';

    const IN = 'in';

    const LIKE = 'like';

    const SOME = 'some';
}