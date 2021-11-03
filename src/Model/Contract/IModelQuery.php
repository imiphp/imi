<?php

declare(strict_types=1);

namespace Imi\Model\Contract;

use Imi\Db\Query\Interfaces\IQuery;

interface IModelQuery extends IQuery
{
    /**
     * 关联查询预加载.
     *
     * @param string|array|null $field
     */
    public function with($field): self;

    /**
     * 指定查询出的模型可序列化的字段.
     *
     * @param string $fields
     */
    public function withField(...$fields): self;
}
