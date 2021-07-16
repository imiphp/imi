<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Mysql\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IWhere;
use Imi\Db\Query\Traits\TRaw;

class Where extends BaseWhere implements IWhere
{
    use TRaw;

    /**
     * 字段名.
     */
    protected ?string $fieldName = null;

    /**
     * 比较符.
     */
    protected ?string $operation = null;

    /**
     * 值
     *
     * @var mixed
     */
    protected $value;

    /**
     * 绑定的数据们.
     */
    protected array $binds = [];

    /**
     * @param mixed $value
     */
    public function __construct(?string $fieldName = null, ?string $operation = null, $value = null, string $logicalOperator = LogicalOperator::AND)
    {
        $this->fieldName = $fieldName;
        $this->operation = $operation;
        $this->value = $value;
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * 字段名.
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    /**
     * 比较符.
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * 值
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 逻辑运算符.
     */
    public function getLogicalOperator(): string
    {
        return $this->logicalOperator;
    }

    /**
     * 字段名.
     */
    public function setFieldName(?string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * 比较符.
     */
    public function setOperation(?string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * 值
     *
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * 逻辑运算符.
     */
    public function setLogicalOperator(string $logicalOperator): void
    {
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * 获取无逻辑的字符串.
     */
    public function toStringWithoutLogic(IQuery $query): string
    {
        $binds = &$this->binds;
        $binds = [];
        $thisValues = &$this->value;
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }
        $operation = $this->operation;
        $result = $query->fieldQuote($this->fieldName) . ' ' . $operation . ' ';
        switch (strtolower($operation))
        {
            case 'between':
            case 'not between':
                $begin = $query->getAutoParamName();
                $end = $query->getAutoParamName();
                $result .= "{$begin} and {$end}";
                $binds[$begin] = $thisValues[0];
                $binds[$end] = $thisValues[1];
                break;
            case 'in':
            case 'not in':
                $result .= '(';
                $valueNames = [];
                foreach ($thisValues as $value)
                {
                    $paramName = $query->getAutoParamName();
                    $valueNames[] = $paramName;
                    $binds[$paramName] = $value;
                }
                $result .= implode(',', $valueNames) . ')';
                break;
            default:
                $value = $query->getAutoParamName();
                $result .= $value;
                $binds[$value] = $thisValues;
                break;
        }

        return $result;
    }

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        return $this->binds;
    }
}
