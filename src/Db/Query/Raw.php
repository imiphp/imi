<?php

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IBase;
use Imi\Db\Query\Traits\TRaw;

class Raw implements IBase
{
    use TRaw;

    public function __construct(string $raw)
    {
        $this->setRawSQL($raw);
    }

    public function __toString()
    {
        return $this->rawSQL;
    }

    /**
     * 获取绑定的数据们.
     *
     * @return array
     */
    public function getBinds()
    {
        return [];
    }
}
