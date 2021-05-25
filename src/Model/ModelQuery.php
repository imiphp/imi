<?php

namespace Imi\Model;

use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Query;

/**
 * 模型查询器.
 */
class ModelQuery extends Query
{
    /**
     * 查询前的字段数量.
     *
     * @var int
     */
    protected $beforeSelectFieldsCount = 0;

    /**
     * @return void
     */
    public function __init()
    {
        parent::__init();
        if ($this->modelClass && $tableName = $this->modelClass::__getMeta()->getTableName())
        {
            $this->table($tableName);
        }
        $this->setResultClass(ModelQueryResult::class);
    }

    /**
     * 查询记录.
     *
     * @return IResult
     */
    public function select(): IResult
    {
        $this->beforeSelectFieldsCount = 0;
        if (!$this->option->field)
        {
            /** @var \Imi\Model\Meta $meta */
            $meta = $this->modelClass::__getMeta();
            if ($sqlColumns = $meta->getSqlColumns())
            {
                $this->field($meta->getTableName() . '.*');
                $fields = $meta->getFields();
                foreach ($sqlColumns as $name => $sqlAnnotations)
                {
                    $sqlAnnotation = $sqlAnnotations[0];
                    $this->fieldRaw($sqlAnnotation->sql, $fields[$name]->name ?? $name);
                }
                $this->beforeSelectFieldsCount = \count($sqlColumns) + 1;
            }
        }

        return parent::select();
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
        if (isset($field[$this->beforeSelectFieldsCount]))
        {
            $result->setIsSetSerializedFields(true);
        }

        return $result;
    }
}
