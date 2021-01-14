<?php

declare(strict_types=1);

namespace Imi\Log;

class Record
{
    /**
     * 日志等级.
     *
     * @var string
     */
    private string $level = '';

    /**
     * 消息.
     *
     * @var string
     */
    private string $message = '';

    /**
     * 上下文.
     *
     * @var array
     */
    private array $context = [];

    /**
     * 代码调用跟踪.
     *
     * @var array
     */
    private array $trace = [];

    /**
     * 日志时间戳.
     *
     * @var int
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
     *
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Get 消息.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get 上下文.
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get 代码调用跟踪.
     *
     * @return array
     */
    public function getTrace(): array
    {
        return $this->trace;
    }

    /**
     * Get 日志时间戳.
     *
     * @return int
     */
    public function getLogTime(): int
    {
        return $this->logTime;
    }
}
