<?php

namespace Imi\Queue\Driver;

use Imi\Queue\Contract\IMessage;
use Imi\Queue\Model\QueueStatus;

/**
 * 队列驱动接口.
 */
interface IQueueDriver
{
    /**
     * 获取队列名称.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 推送消息到队列，返回消息ID.
     *
     * @param \Imi\Queue\Contract\IMessage $message
     * @param float                        $delay
     * @param array                        $options
     *
     * @return string
     */
    public function push(IMessage $message, float $delay = 0, array $options = []): string;

    /**
     * 从队列弹出一个消息.
     *
     * @param float $timeout
     *
     * @return \Imi\Queue\Contract\IMessage|null
     */
    public function pop(float $timeout = 0): ?IMessage;

    /**
     * 删除一个消息.
     *
     * @param \Imi\Queue\Contract\IMessage $message
     *
     * @return bool
     */
    public function delete(IMessage $message): bool;

    /**
     * 清空队列.
     *
     * @param int|int[]|null $queueType 清空哪个队列，默认为全部
     *
     * @return void
     */
    public function clear($queueType = null);

    /**
     * 将消息标记为成功
     *
     * @param \Imi\Queue\Contract\IMessage $message
     *
     * @return void
     */
    public function success(IMessage $message);

    /**
     * 将消息标记为失败.
     *
     * @param \Imi\Queue\Contract\IMessage $message
     * @param bool                         $requeue
     *
     * @return void
     */
    public function fail(IMessage $message, bool $requeue = false);

    /**
     * 获取队列状态
     *
     * @return \Imi\Queue\Model\QueueStatus
     */
    public function status(): QueueStatus;

    /**
     * 将失败消息恢复到队列.
     *
     * 返回恢复数量
     *
     * @return int
     */
    public function restoreFailMessages(): int;

    /**
     * 将超时消息恢复到队列.
     *
     * 返回恢复数量
     *
     * @return int
     */
    public function restoreTimeoutMessages(): int;
}
