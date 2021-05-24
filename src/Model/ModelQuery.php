<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Query;

/**
 * 模型查询器.
 */
class ModelQuery extends Query
{
    public function __init(): void
    {
        parent::__init();
        if ($this->modelClass && $tableName = $this->modelClass::__getMeta()->getTableName())
        {
            $this->table($tableName);
        }
        $this->setResultClass(ModelQueryResult::class);
    }

    /**
     * 执行SQL语句.
     *
     * @param string $sql
     *
     * @return IResult
     */
    public function execute($sql)
    {
        $field = $this->option->field;
        /** @var ModelQueryResult $result */
        $result = parent::execute($sql);
        if ($field)
        {
            $result->setIsSetSerializedFields(true);
        }

        return $result;
    }
}
