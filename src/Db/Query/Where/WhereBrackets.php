<?php
namespace Imi\Db\Query\Where;

use Imi\Db\Query\Traits\TRaw;
use Imi\Db\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IWhere;
use Imi\Db\Query\Interfaces\IBaseWhere;
use Imi\Db\Query\Interfaces\IWhereBrackets;

class WhereBrackets extends BaseWhere implements IWhereBrackets
{
    use TRaw;
    
    /**
     * 回调
     * @var callable
     */
    protected $callback;

    /**
     * 逻辑运算符
     * @var string
     */
    protected $logicalOperator;

    /**
     * 绑定的数据们
     * @var array
     */
    protected $binds = [];

    public function __construct(callable $callback = null, string $logicalOperator = LogicalOperator::AND)
    {
        $this->callback = $callback;
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * 回调
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * 逻辑运算符
     * @return string
     */
    public function getLogicalOperator(): string
    {
        return $this->logicalOperator;
    }
    
    /**
     * 回调
     * @param callable $callback
     * @return void
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * 逻辑运算符
     * @param string $logicalOperator
     * @return void
     */
    public function setLogicalOperator(string $logicalOperator)
    {
        $this->logicalOperator = $logicalOperator;
    }

    public function toStringWithoutLogic()
    {
        if($this->isRaw)
        {
            return $this->rawSQL;
        }
        $callResult = ($this->callback)();
        if(is_array($callResult))
        {
            $result = '(';
            foreach($callResult as $i => $callResultItem)
            {
                if($callResultItem instanceof IBaseWhere)
                {
                    if(0 === $i)
                    {
                        $result .= $callResultItem->toStringWithoutLogic() . ' ';
                    }
                    else
                    {
                        $result .= $callResultItem . ' ';
                    }
                    $this->binds = array_merge($this->binds, $callResultItem->getBinds());
                }
                else
                {
                    $result .= $callResultItem . ' ';
                }
            }
            return $result . ')';
        }
        else if($callResult instanceof IBaseWhere)
        {
            $result = '(' . $callResult . ')';
            $this->binds = $callResult->getBinds();
            return $result;
        }
        else
        {
            return (string)$callResult;
        }
    }

    /**
     * 获取绑定的数据们
     * @return array
     */
    public function getBinds()
    {
        return $this->binds;
    }
}