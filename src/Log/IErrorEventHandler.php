<?php

declare(strict_types=1);

namespace Imi\Log;

interface IErrorEventHandler
{
    public function isPropagationStopped(): bool;

    /**
     * 取消系统内部的错误域异常处理并停止后续处理器执行.
     */
    public function stopPropagation(bool $stop = true): void;

    public function handleError(int $errNo, string $errStr, string $errFile, int $errLine): void;

    public function handleException(\Throwable $throwable): void;
}
