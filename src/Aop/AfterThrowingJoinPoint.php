<?php

declare(strict_types=1);

namespace Imi\Aop;

class AfterThrowingJoinPoint extends JoinPoint
{
    /**
     * 是否取消抛出异常.
     */
    private bool $isCancelThrow = false;

    public function __construct(string $type, string $method, array &$args, object $target, /**
     * 异常.
     */
        private readonly ?\Throwable $throwable)
    {
        parent::__construct($type, $method, $args, $target);
    }

    /**
     * 获取异常.
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * 取消抛出异常.
     *
     * @param bool $isCancelThrow 是否取消，默认为true
     */
    public function cancelThrow(bool $isCancelThrow = true): void
    {
        $this->isCancelThrow = $isCancelThrow;
    }

    /**
     * 是否取消抛出异常.
     */
    public function isCancelThrow(): bool
    {
        return $this->isCancelThrow;
    }
}
