<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ConnectionContext\StoreHandler\Local;

/**
 * @Bean("ConnectionContextAmqp")
 */
class Amqp extends Local
{
    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void
    {
        $needBind = !$this->getClientIdByFlag($flag);
        parent::bind($flag, $clientId);
        if ($needBind)
        {
            /** @var \Imi\Swoole\Server\Util\Amqp\AmqpServerConsumer $amqpServerConsumer */
            $amqpServerConsumer = RequestContext::getServerBean('AmqpServerConsumer');
            $amqpServerConsumer->bindRoutingKey('flag.' . $flag);
        }
    }

    /**
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        parent::unbind($flag, $clientId, $keepTime);
        if (!$this->getClientIdByFlag($flag))
        {
            /** @var \Imi\Swoole\Server\Util\Amqp\AmqpServerConsumer $amqpServerConsumer */
            $amqpServerConsumer = RequestContext::getServerBean('AmqpServerConsumer');
            $amqpServerConsumer->unbindRoutingKey('flag.' . $flag);
        }
    }
}
