<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IWhere;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Traits\TRaw;

class Where extends BaseWhere implements IWhere
{
    use TRaw;
    use TKeyword;

    /**
     * 字段名.
     *
     * @var string|null
     */
    protected ?string $fieldName;

    /**
     * 比较符.
     *
     * @var string|null
     */
    protected ?string $operation;

    /**
     * 值
     *
     * @var mixed
     */
    protected $value;

    /**
     * 绑定的数据们.
     *
     * @var array
     */
    protected array $binds = [];

    public function __construct(?string $fieldName = null, ?string $operation = null, $value = null, string $logicalOperator = LogicalOperator::AND)
    {
        $this->fieldName = $fieldName;
        $this->operation = $operation;
        $this->value = $value;
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * 字段名.
     *
     * @return string|null
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    /**
     * 比较符.
     *
     * @return string|null
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
     *
     * @return string
     */
    public function getLogicalOperator(): string
    {
        return $this->logicalOperator;
    }

    /**
     * 字段名.
     *
     * @param string|null $fieldName
     *
     * @return void
     */
    public function setFieldName(?string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * 比较符.
     *
     * @param string|null $operation
     *
     * @return void
     */
    public function setOperation(?string $operation)
    {
        $this->operation = $operation;
    }

    /**
     * 值
     *
     * @param mixed $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * 逻辑运算符.
     *
     * @param string $logicalOperator
     *
     * @return void
     */
    public function setLogicalOperator(string $logicalOperator)
    {
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * 获取无逻辑的字符串.
     *
     * @param IQuery $query
     *
     * @return string
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
        $result = $this->parseKeyword($this->fieldName) . ' ' . $operation . ' ';
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
     *
     * @return array
     */
    public function getBinds(): array
    {
        return $this->binds;
    }
}
