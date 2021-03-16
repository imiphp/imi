<?php

declare(strict_types=1);

namespace Imi\Log;

class Record
{
    /**
     * 日志等级.
     */
    private string $level = '';

    /**
     * 消息.
     */
    private string $message = '';

    /**
     * 上下文.
     */
    private array $context = [];

    /**
     * 代码调用跟踪.
     */
    private array $trace = [];

    /**
     * 日志时间戳.
     */
    private int $logTime = 0;

    public function __construct(string $level, string $message, array $context, array $trace, int $logTime)
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
        $this->trace = $trace;
        $this->logTime = $logTime;
    }

    /**
     * Get 日志等级.
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Get 消息.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get 上下文.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get 代码调用跟踪.
     */
    public function getTrace(): array
    {
        return $this->trace;
    }

    /**
     * Get 日志时间戳.
     */
    public function getLogTime(): int
    {
        return $this->logTime;
    }
}
