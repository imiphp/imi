<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Group\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Group\Handler\Local;

/**
 * @Bean(name="GroupAmqp", env="swoole")
 */
class Amqp extends Local
{
    /**
     * {@inheritDoc}
     */
    public function createGroup(string $groupName, int $maxClients = -1): void
    {
        if ($this->hasGroup($groupName))
        {
            return;
        }
        parent::createGroup($groupName, $maxClients);
        /** @var \Imi\Swoole\Server\Util\Amqp\AmqpServerConsumer $amqpServerConsumer */
        $amqpServerConsumer = RequestContext::getServerBean('AmqpServerConsumer');
        $amqpServerConsumer->bindRoutingKey('group.' . $groupName);
    }

    /**
     * {@inheritDoc}
     */
    public function closeGroup(string $groupName): void
    {
        parent::closeGroup($groupName);
        /** @var \Imi\Swoole\Server\Util\Amqp\AmqpServerConsumer $amqpServerConsumer */
        $amqpServerConsumer = RequestContext::getServerBean('AmqpServerConsumer');
        $amqpServerConsumer->unbindRoutingKey('group.' . $groupName);
    }
}
