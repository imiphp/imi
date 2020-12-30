<?php

declare(strict_types=1);

namespace Imi\Aop;

class AfterThrowingJoinPoint extends JoinPoint
{
    /**
     * 异常.
     *
     * @var \Throwable
     */
    private \Throwable $throwable;

    /**
     * 是否取消抛出异常.
     *
     * @var bool
     */
    private bool $isCancelThrow = false;

    public function __construct(string $type, string $method, array &$args, object $target, \Throwable $throwable)
    {
        parent::__construct($type, $method, $args, $target);
        $this->throwable = $throwable;
    }

    /**
     * 获取异常.
     *
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * 取消抛出异常.
     *
     * @param bool $isCancelThrow 是否取消，默认为true
     *
     * @return void
     */
    public function cancelThrow(bool $isCancelThrow = true)
    {
        $this->isCancelThrow = $isCancelThrow;
    }

    /**
     * 是否取消抛出异常.
     *
     * @return bool
     */
    public function isCancelThrow(): bool
    {
        return $this->isCancelThrow;
    }
}
