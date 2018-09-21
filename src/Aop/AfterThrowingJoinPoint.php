<?php
namespace Imi\Aop;

class AfterThrowingJoinPoint extends JoinPoint
{
    /**
     * 异常
     * @var \Throwable
     */
    private $throwable;

    /**
     * 是否取消抛出异常
     * @var boolean
     */
    private $isCancelThrow = false;

    public function __construct($type, $method, $args, $target, $_this, \Throwable $throwable)
    {
        parent::__construct(...func_get_args());
        $this->throwable = $throwable;
    }

    /**
     * 获取异常
     * @return \Throwable
     */
    public function getThrowable()
    {
        return $this->throwable;
    }

    /**
     * 取消抛出异常
     * @param boolean $isCancelThrow 是否取消，默认为true
     * @return boolean
     */
    public function cancelThrow($isCancelThrow = true)
    {
        $this->isCancelThrow = $isCancelThrow;
    }

    /**
     * 是否取消抛出异常
     * @return boolean
     */
    public function isCancelThrow()
    {
        return $this->isCancelThrow;
    }
}