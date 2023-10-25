<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ConnectionContext\StoreHandler\Local;

#[Bean(name: 'ConnectionContextAmqp', env: 'swoole', recursion: false)]
class Amqp extends Local
{
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
