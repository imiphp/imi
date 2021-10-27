<?php

declare(strict_types=1);

namespace Imi\Model\Traits;

use Imi\Db\Query\Field;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Model\ModelQueryResult;

trait TModelQuery
{
    /**
     * 是否设置序列化字段.
     */
    protected bool $isSetSerializedFields = false;

    public function __init(): void
    {
        parent::__init();
        $modelClass = $this->modelClass;
        if ($modelClass)
        {
            /** @var \Imi\Model\Meta $meta */
            $meta = $modelClass::__getMeta();
            $tableName = $meta->getTableName();
            if (null !== $tableName)
            {
                $this->table($tableName, null, $meta->getDatabaseName());
            }
        }
        $this->setResultClass(ModelQueryResult::class);
    }

    /**
     * 查询记录.
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

        $k = array_key_first($field);
        $v = $field[$k] ?? null;

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
                $field->setValue($v ?? '', $this);
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
     */
    public function execute(string $sql): IResult
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
