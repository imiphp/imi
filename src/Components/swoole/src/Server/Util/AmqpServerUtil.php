<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\AMQP\Contract\IConsumer;
use Imi\AMQP\Contract\IPublisher;
use Imi\AMQP\Message;
use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\ErrorLog;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Worker;

if (class_exists(\Imi\AMQP\Main::class))
{
    /**
     * @Bean(name="AmqpServerUtil", env="swoole")
     */
    class AmqpServerUtil extends LocalServerUtil
    {
        /**
         * amqp 连接名称.
         */
        protected ?string $amqpName = null;

        /**
         * 交换机配置.
         */
        protected array $exchangeConfig = [];

        /**
         * 队列配置.
         */
        protected array $queueConfig = [];

        /**
         * 消费者类.
         */
        protected string $consumerClass = 'AmqpServerConsumer';

        /**
         * 发布者类.
         */
        protected string $publisherClass = 'AmqpServerPublisher';

        /**
         * @Inject
         */
        protected ErrorLog $errorLog;

        protected bool $subscribeEnable = true;

        protected IConsumer $consumerInstance;

        protected IPublisher $publisherInstance;

        protected string $serverName;

        public function __init(): void
        {
            $server = RequestContext::getServer();
            $this->serverName = $server->getName();
            $this->consumerInstance = $server->getBean($this->consumerClass);
            $this->publisherInstance = $server->getBean($this->publisherClass);
            Event::one('IMI.MAIN_SERVER.WORKER.EXIT', function () {
                $this->subscribeEnable = false;
            });
            $this->startSubscribe();
        }

        public function sendAmqpMessage(string $action, array $data = [], string $routingKey = ''): bool
        {
            $data['action'] = $action;
            $data['workerId'] = Worker::getWorkerId();
            $message = json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
            $amqpMessage = new Message();
            $amqpMessage->setBody($message);
            $amqpMessage->setRoutingKey($routingKey);

            return $this->publisherInstance->publish($amqpMessage);
        }

        /**
         * {@inheritDoc}
         */
        public function sendByFlag($data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
        {
            $server = $this->getServer($serverName);
            /** @var \Imi\Server\DataParser\DataParser $dataParser */
            $dataParser = $server->getBean(DataParser::class);
            if (null === $serverName)
            {
                $serverName = $server->getName();
            }

            return $this->sendRawByFlag($dataParser->encode($data, $serverName), $flag, $serverName, $toAllWorkers);
        }

        /**
         * {@inheritDoc}
         */
        public function sendRawByFlag(string $data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
        {
            if (null === $flag)
            {
                $clientId = ConnectionContext::getClientId();
                if (!$clientId)
                {
                    return 0;
                }
                $clientIds = [(int) $clientId];

                return $this->sendRaw($data, $clientIds, $serverName, $toAllWorkers);
            }
            elseif ($toAllWorkers)
            {
                $server = $this->getServer($serverName);
                $success = 0;
                foreach ((array) $flag as $tmpFlag)
                {
                    $id = uniqid('', true);
                    if ($this->sendAmqpMessage('sendRawByFlag', [
                        'messageId'  => $id,
                        'flag'       => $tmpFlag,
                        'data'       => $data,
                        'serverName' => $server->getName(),
                    ], 'flag.' . $tmpFlag))
                    {
                        ++$success;
                    }
                }

                return $success;
            }
            else
            {
                $clientIds = [];
                foreach ((array) $flag as $tmpFlag)
                {
                    $clientId = ConnectionContext::getClientIdByFlag($tmpFlag, $serverName);
                    if ($clientId)
                    {
                        $clientIds = array_merge($clientIds, $clientId);
                    }
                }
                if (!$clientIds)
                {
                    return 0;
                }

                return $this->sendRaw($data, $clientIds, $serverName, $toAllWorkers);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
        {
            $server = $this->getServer($serverName);
            $swooleServer = $server->getSwooleServer();
            $success = 0;
            if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
            {
                $method = 'push';
            }
            else
            {
                $method = 'send';
            }
            if ($toAllWorkers)
            {
                $id = uniqid('', true);
                if ($this->sendAmqpMessage('sendRawToAll', [
                    'messageId'  => $id,
                    'data'       => $data,
                    'serverName' => $server->getName(),
                ], 'all'))
                {
                    ++$success;
                }
            }
            else
            {
                foreach ($server->getSwoolePort()->connections as $clientId)
                {
                    /** @var \Swoole\WebSocket\Server $swooleServer */
                    if ('push' === $method && !$swooleServer->isEstablished($clientId))
                    {
                        continue;
                    }
                    if ($swooleServer->$method($clientId, $data))
                    {
                        ++$success;
                    }
                }
            }

            return $success;
        }

        /**
         * {@inheritDoc}
         */
        public function sendRawToGroup($groupName, string $data, ?string $serverName = null, bool $toAllWorkers = true): int
        {
            $server = $this->getServer($serverName);
            $groups = (array) $groupName;
            $success = 0;
            if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
            {
                $method = 'push';
            }
            else
            {
                $method = 'send';
            }
            if ($toAllWorkers)
            {
                foreach ($groups as $tmpGroup)
                {
                    $id = uniqid('', true);
                    if ($this->sendAmqpMessage('sendRawToGroup', [
                        'messageId'  => $id,
                        'group'      => $tmpGroup,
                        'data'       => $data,
                        'serverName' => $server->getName(),
                    ], 'group.' . $tmpGroup))
                    {
                        ++$success;
                    }
                }

                return $success;
            }
            else
            {
                foreach ($groups as $tmpGroupName)
                {
                    $group = $server->getGroup($tmpGroupName);
                    if ($group)
                    {
                        $result = $group->$method($data);
                        foreach ($result as $item)
                        {
                            if ($item)
                            {
                                ++$success;
                            }
                        }
                    }
                }
            }

            return $success;
        }

        /**
         * {@inheritDoc}
         */
        public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
        {
            $server = $this->getServer($serverName);
            $swooleServer = $server->getSwooleServer();
            $result = 0;
            if ($toAllWorkers)
            {
                foreach ((array) $flag as $tmpFlag)
                {
                    $id = uniqid('', true);
                    if ($this->sendAmqpMessage('closeByFlag', [
                        'messageId'  => $id,
                        'flag'       => $tmpFlag,
                        'serverName' => $server->getName(),
                    ], 'flag.' . $tmpFlag))
                    {
                        ++$result;
                    }
                }
            }
            else
            {
                if (null === $flag)
                {
                    $clientIds = [ConnectionContext::getClientId()];
                }
                else
                {
                    $clientIds = ConnectionContext::getClientIdByFlag($flag, $serverName);
                    if (!$clientIds)
                    {
                        return 0;
                    }
                }
                foreach ($clientIds as $clientId)
                {
                    if ($swooleServer->close((int) $clientId))
                    {
                        ++$result;
                    }
                }
            }

            return $result;
        }

        public function startSubscribe(): void
        {
            $server = RequestContext::getServer();
            if ($this->subscribeEnable && $server && $server->isLongConnection())
            {
                imigo(function () {
                    try
                    {
                        $this->consumerInstance->run();
                    }
                    catch (\Throwable $th)
                    {
                        /** @var \Imi\Log\ErrorLog $errorLog */
                        $errorLog = App::getBean('ErrorLog');
                        $errorLog->onException($th);
                        sleep(1);
                        $this->startSubscribe();
                    }
                });
            }
        }

        public function isSubscribeEnable(): bool
        {
            return $this->subscribeEnable;
        }

        public function getConsumerInstance(): IConsumer
        {
            return $this->consumerInstance;
        }

        public function getPublisherInstance(): IPublisher
        {
            return $this->publisherInstance;
        }

        public function getAmqpName(): ?string
        {
            return $this->amqpName;
        }

        public function getExchangeConfig(): array
        {
            return $this->exchangeConfig;
        }

        public function getQueueConfig(): array
        {
            return $this->queueConfig;
        }
    }
}
