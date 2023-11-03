<?php

declare(strict_types=1);

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
     */
    public function setReturnValue(mixed $value): void
    {
        $this->returnValue = $value;
    }

    /**
     * 获取返回值
     */
    public function getReturnValue(): mixed
    {
        return $this->returnValue;
    }
}
