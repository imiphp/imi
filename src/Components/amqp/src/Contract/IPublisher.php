<?php

namespace Imi\AMQP\Contract;

/**
 * 发布者.
 */
interface IPublisher
{
    /**
     * 发布消息.
     *
     * @param \Imi\AMQP\Contract\IMessage $message
     *
     * @return void
     */
    public function publish(IMessage $message);
}
