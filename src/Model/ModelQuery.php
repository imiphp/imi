<?php

namespace Imi\Model;

use Imi\Db\Query\Field;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Query;

/**
 * 模型查询器.
 */
class ModelQuery extends Query
{
    /**
     * 是否设置序列化字段.
     *
     * @var bool
     */
    protected $isSetSerializedFields = false;

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
        if ($this->hasCustomFields())
        {
            $this->isSetSerializedFields = true;
        }
        else
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
            }
            $this->isSetSerializedFields = false;
        }

        return parent::select();
    }

    private function hasCustomFields(): bool
    {
        $field = $this->option->field;
        if (!$field)
        {
            return false;
        }
        if (\count($field) > 1)
        {
            return true;
        }

        $k = key($field);
        $v = current($field);

        if (is_numeric($k))
        {
            if ('*' === $v)
            {
                return false;
            }
            if ($v instanceof Field)
            {
                $field = $v;
            }
            else
            {
                $field = new Field();
                $field->setValue($v);
            }
        }
        else
        {
            $field = new Field(null, null, $k, $v);
        }
        if ('*' !== $field->getField())
        {
            return true;
        }
        $table = $field->getTable();
        $tableObject = $this->option->table;
        if (null === $table || $table === $tableObject->getTable() || $table === $tableObject->getAlias())
        {
            return false;
        }

        return true;
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
        /** @var ModelQueryResult $result */
        $result = parent::execute($sql);
        if ($this->isSetSerializedFields)
        {
            $result->setIsSetSerializedFields(true);
        }

        return $result;
    }
}
