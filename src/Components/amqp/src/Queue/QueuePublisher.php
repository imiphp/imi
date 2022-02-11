<?php

declare(strict_types=1);

namespace Imi\AMQP\Queue;

use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Publisher;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BasePublisher;

class QueuePublisher extends BasePublisher
{
    public function __construct(array $exchanges, array $queues, array $publishers, ?string $poolName = null)
    {
        parent::__construct();

        $this->poolName = $poolName;

        $list = [];
        foreach ($exchanges as $exchange)
        {
            $list[] = new Exchange($exchange);
        }
        $this->exchanges = $list;

        $list = [];
        foreach ($queues as $queue)
        {
            $list[] = new Queue($queue);
        }
        $this->queues = $list;

        $list = [];
        foreach ($publishers as $publisher)
        {
            $list[] = new Publisher($publisher);
        }
        $this->publishers = $list;
    }

    /**
     * {@inheritDoc}
     */
    protected function initConfig(): void
    {
    }
}
