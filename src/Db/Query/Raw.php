<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IBase;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TRaw;

class Raw implements IBase
{
    use TRaw;

    public function __construct(string $raw)
    {
        $this->setRawSQL($raw);
    }

    public function toString(IQuery $query): string
    {
        return $this->rawSQL;
    }

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        return [];
    }
}
