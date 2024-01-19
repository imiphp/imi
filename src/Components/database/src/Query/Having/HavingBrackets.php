<?php

declare(strict_types=1);

namespace Imi\Db\Query\Having;

use Imi\Db\Query\Interfaces\IHavingBrackets;
use Imi\Db\Query\Where\WhereBrackets;

class HavingBrackets extends WhereBrackets implements IHavingBrackets
{
}
