<?php

namespace Imi\Queue\Model;

use Imi\Queue\Contract\IMessage;

/**
 * 消息.
 */
class Message implements IMessage
{
    /**
     * 消息 ID.
     *
     * @var string
     */
    protected $messageId = '';

    /**
     * 已重试次数.
     *
     * @var int
     */
    protected $retryCount = 0;

    /**
     * 最大重试次数.
     *
     * @var int
     */
    protected $maxRetryCount = 0;

    /**
     * 消息内容.
     *
     * @var string
     */
    protected $message;

    /**
     * 工作超时时间，单位：秒.
     *
     * @var float
     */
    protected $workingTimeout = 0;

    /**
     * 获取消息 ID.
     *
     * @return string
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * 设置消息 ID.
     *
     * @param string $messageId
     *
     * @return void
     */
    public function setMessageId(string $messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * 获取消息内容.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * 设置消息内容.
     *
     * @param string $message
     *
     * @return void
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * 获取工作超时时间，单位：秒.
     *
     * @return float
     */
    public function getWorkingTimeout(): float
    {
        return $this->workingTimeout;
    }

    /**
     * 设置工作超时时间，单位：秒.
     *
     * @param float $workingTimeout
     *
     * @return void
     */
    public function setWorkingTimeout(float $workingTimeout)
    {
        $this->workingTimeout = $workingTimeout;
    }

    /**
     * 获取已重试次数.
     *
     * @return int
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * 获取重试次数.
     *
     * @param int $retryCount
     *
     * @return void
     */
    public function setRetryCount(int $retryCount)
    {
        $this->retryCount = $retryCount;
    }

    /**
     * 获取最大重试次数.
     *
     * @return int
     */
    public function getMaxRetryCount(): int
    {
        return $this->maxRetryCount;
    }

    /**
     * 获取最大重试次数.
     *
     * @param int $maxRetryCount
     *
     * @return void
     */
    public function setMaxRetryCount(int $maxRetryCount)
    {
        $this->maxRetryCount = $maxRetryCount;
    }

    /**
     * 将当前对象作为数组返回.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'messageId'      => $this->getMessageId(),
            'retryCount'     => $this->getRetryCount(),
            'maxRetryCount'  => $this->getMaxRetryCount(),
            'message'        => $this->getMessage(),
            'workingTimeout' => $this->getWorkingTimeout(),
        ];
    }

    /**
     * 从数组加载数据.
     *
     * @param array $data
     *
     * @return void
     */
    public function loadFromArray(array $data)
    {
        foreach ($data as $k => $v)
        {
            $this->$k = $v;
        }
    }
}
