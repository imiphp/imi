<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util\Amqp;

use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Base\BasePublisher;
use Imi\AMQP\Pool\AMQPPool;
use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Swoole\Server\Util\AmqpServerUtil;

if (class_exists(\Imi\AMQP\Main::class))
{
    /**
     * @Bean("AmqpServerPublisher")
     */
    class AmqpServerPublisher extends BasePublisher
    {
        protected AmqpServerUtil $amqpServerUtil;

        /**
         * {@inheritDoc}
         */
        public function initConfig(): void
        {
            /** @var AmqpServerUtil $amqpServerUtil */
            $amqpServerUtil = $this->amqpServerUtil = RequestContext::getServerBean('AmqpServerUtil');
            $this->exchanges = [new Exchange($amqpServerUtil->getExchangeConfig())];
            $this->poolName = $amqpServerUtil->getAmqpName() ?? AMQPPool::getDefaultPoolName();
        }
    }
}
