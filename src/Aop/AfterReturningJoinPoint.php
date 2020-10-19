<?php

namespace Imi\Aop;

class AfterReturningJoinPoint extends JoinPoint
{
    /**
     * 返回值
     *
     * @var mixed
     */
    private $returnValue;

    /**
     * 设置返回值
     *
     * @return mixed
     */
    public function setReturnValue($value)
    {
        $this->returnValue = $value;
    }

    /**
     * 获取返回值
     *
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }
}
