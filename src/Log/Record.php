<?php

namespace Imi\Log;

class Record
{
    /**
     * 日志等级.
     *
     * @var string
     */
    private $level;

    /**
     * 消息.
     *
     * @var mixed
     */
    private $message;

    /**
     * 上下文.
     *
     * @var array
     */
    private $context;

    /**
     * 代码调用跟踪.
     *
     * @var array
     */
    private $trace;

    /**
     * 日志时间戳.
     *
     * @var int
     */
    private $logTime;

    public function __construct($level, $message, array $context, $trace, $logTime)
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
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get 消息.
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get 上下文.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get 代码调用跟踪.
     *
     * @return array
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * Get 日志时间戳.
     *
     * @return int
     */
    public function getLogTime()
    {
        return $this->logTime;
    }
}
