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
    /**
     * 是否设置序列化字段.
     *
     * @var bool
     */
    protected $isSetSerializedFields = false;

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
     * 查询记录.
     *
     * @return IResult
     */
    public function select(): IResult
    {
        if ($this->option->field)
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
