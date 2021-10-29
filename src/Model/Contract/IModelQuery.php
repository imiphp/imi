<?php

declare(strict_types=1);

namespace Imi\Model\Contract;

use Imi\Db\Query\Interfaces\IQuery;

interface IModelQuery extends IQuery
{
    /**
     * 关联查询预加载.
     *
     * @param string|array $field
     */
    public function with($field): self;
}
