<?php

declare(strict_types=1);

namespace Imi\Log;

abstract class AbstractErrorEventHandler implements IErrorEventHandler
{
    private bool $stopPropagation = false;

    /**
     * 是否取消系统内部的错误域异常处理并停止后续处理器执行.
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }

    /**
     * 取消系统内部的错误域异常处理并停止后续处理器执行.
     */
    public function stopPropagation(bool $stop = true): void
    {
        $this->stopPropagation = $stop;
    }
}
