<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util\Amqp;

use Imi\AMQP\Annotation\Consumer;
use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BaseConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Enum\ConsumerResult;
use Imi\AMQP\Pool\AMQPPool;
use Imi\Bean\Annotation\Bean;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\Server;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Util\AmqpServerUtil;
use Imi\Worker;

if (class_exists(\Imi\AMQP\Main::class))
{
    #[Bean(name: 'AmqpServerConsumer', env: 'swoole')]
    class AmqpServerConsumer extends BaseConsumer
    {
        public function __construct(protected ?AmqpServerUtil $amqpServerUtil = null)
        {
            parent::__construct();
        }

        /**
         * {@inheritDoc}
         */
        public function initConfig(): void
        {
            /** @var AmqpServerUtil $amqpServerUtil */
            $amqpServerUtil = ($this->amqpServerUtil ??= RequestContext::getServerBean('AmqpServerUtil'));
            $this->exchanges = [$exchangeAnnotation = new Exchange(...$amqpServerUtil->getExchangeConfig())];
            $queueConfig = $amqpServerUtil->getQueueConfig();
            $queueName = ($queueConfig['name'] .= Worker::getWorkerId());
            $this->queues = [new Queue(...$queueConfig)];
            $consumerAnnotation = new Consumer();
            $consumerAnnotation->queue = $queueName;
            $consumerAnnotation->exchange = $exchangeAnnotation->name;
            $consumerAnnotation->routingKey = 'all';
            $this->consumers = [$consumerAnnotation];
            $this->poolName = $amqpServerUtil->getAmqpName() ?? AMQPPool::getDefaultPoolName();
        }

        /**
         * 绑定路由键.
         */
        public function bindRoutingKey(string $routingKey): void
        {
            $channel = $this->getConnection()->channel();
            $channel->queue_bind($this->consumers[0]->queue, $this->exchanges[0]->name, $routingKey);
        }

        /**
         * 解绑路由键.
         */
        public function unbindRoutingKey(string $routingKey): void
        {
            $channel = $this->getConnection()->channel();
            $channel->queue_unbind($this->consumers[0]->queue, $this->exchanges[0]->name, $routingKey);
        }

        /**
         * {@inheritDoc}
         *
         * @return mixed
         */
        protected function consume(IMessage $message)
        {
            try
            {
                $data = json_decode($message->getBody(), true);
                $serverName = $data['serverName'];
                RequestContext::set('server', $server = ServerManager::getServer($serverName));
                match ($data['action'] ?? null)
                {
                    'sendRawByFlag'  => Server::sendRawByFlag($data['data'], $data['flag'], $serverName, false),
                    'closeByFlag'    => Server::closeByFlag($data['flag'], $serverName, false),
                    'sendRawToGroup' => Server::sendRawToGroup($data['group'], $data['data'], $serverName, false),
                    'sendRawToAll'   => Server::sendRawToAll($data['data'], $serverName, false),
                    default          => ConsumerResult::ACK,
                };

                return ConsumerResult::ACK;
            }
            catch (\Throwable $th)
            {
                Log::error($th);

                return ConsumerResult::NACK;
            }
        }
    }
}
