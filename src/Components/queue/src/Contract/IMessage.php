<?php

declare(strict_types=1);

namespace Imi\Queue\Contract;

use Imi\Util\Interfaces\IArrayable;

/**
 * 消息接口.
 */
interface IMessage extends IArrayable
{
    /**
     * 获取消息 ID.
     */
    public function getMessageId(): ?string;

    /**
     * 设置消息 ID.
     *
     * @return void
     */
    public function setMessageId(string $messageId);

    /**
     * 获取消息内容.
     *
     * @return string
     */
    public function getMessage(): ?string;

    /**
     * 设置消息内容.
     *
     * @return void
     */
    public function setMessage(string $message);

    /**
     * 获取工作超时时间，单位：秒.
     */
    public function getWorkingTimeout(): float;

    /**
     * 设置工作超时时间，单位：秒.
     *
     * @return void
     */
    public function setWorkingTimeout(float $workingTimeout);

    /**
     * 获取已重试次数.
     */
    public function getRetryCount(): int;

    /**
     * 获取重试次数.
     *
     * @return void
     */
    public function setRetryCount(int $retryCount);

    /**
     * 获取最大重试次数.
     */
    public function getMaxRetryCount(): int;

    /**
     * 获取最大重试次数.
     *
     * @return void
     */
    public function setMaxRetryCount(int $maxRetryCount);

    /**
     * 从数组加载数据.
     *
     * @return void
     */
    public function loadFromArray(array $data);
}
