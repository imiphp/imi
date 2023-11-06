<?php

declare(strict_types=1);

namespace Imi\Model\Contract;

use Imi\Db\Query\Interfaces\IQuery;

interface IModelQuery extends IQuery
{
    /**
     * 关联查询预加载.
     */
    public function with(string|array|null $field): self;

    /**
     * 指定查询出的模型可序列化的字段.
     */
    public function withField(string ...$fields): self;
}
