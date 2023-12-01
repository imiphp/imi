<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Util;

use Imi\AMQP\Contract\IConsumer;
use Imi\AMQP\Contract\IPublisher;
use Imi\AMQP\Message;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\Contract\IServer;
use Imi\Server\DataParser\DataParser;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Worker;

use function Swoole\Coroutine\defer;

if (class_exists(\Imi\AMQP\Main::class))
{
    #[Bean(name: 'AmqpServerUtil', env: 'swoole')]
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

        protected bool $subscribeEnable = true;

        protected IConsumer $consumerInstance;

        protected IServer $server;

        public function __init(): void
        {
            $this->server = $server = RequestContext::getServer();
            $this->consumerInstance = $server->getBean($this->consumerClass, $this);
            Event::one(SwooleEvents::SERVER_WORKER_EXIT, function (): void {
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

            return $this->getPublisherInstance()->publish($amqpMessage);
        }

        /**
         * {@inheritDoc}
         */
        public function sendByFlag(mixed $data, array|string|null $flag = null, ?string $serverName = null, bool $toAllWorkers = true): int
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
        public function sendRawByFlag(string $data, $flag = null, ?string $serverName = null, bool $toAllWorkers = true): int
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
                    if ($this->sendAmqpMessage('sendRawByFlag', [
                        'messageId'  => bin2hex(random_bytes(16)),
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
                $pushParams = (array) $server->getNonControlFrameType()->value;
            }
            else
            {
                $method = 'send';
                $pushParams = [];
            }
            if ($toAllWorkers)
            {
                if ($this->sendAmqpMessage('sendRawToAll', [
                    'messageId'  => bin2hex(random_bytes(16)),
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
                    if ($swooleServer->{$method}($clientId, $data, ...$pushParams))
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
        public function sendRawToGroup(string|array $groupName, string $data, ?string $serverName = null, bool $toAllWorkers = true): int
        {
            $server = $this->getServer($serverName);
            $groups = (array) $groupName;
            $success = 0;
            if ($server instanceof \Imi\Swoole\Server\WebSocket\Server)
            {
                $method = 'push';
                $pushParams = (array) $server->getNonControlFrameType()->value;
            }
            else
            {
                $method = 'send';
                $pushParams = [];
            }
            if ($toAllWorkers)
            {
                foreach ($groups as $tmpGroup)
                {
                    if ($this->sendAmqpMessage('sendRawToGroup', [
                        'messageId'  => bin2hex(random_bytes(16)),
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
                        $result = $group->{$method}($data, ...$pushParams);
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
        public function closeByFlag(string|array|null $flag, ?string $serverName = null, bool $toAllWorkers = true): int
        {
            $server = $this->getServer($serverName);
            $swooleServer = $server->getSwooleServer();
            $result = 0;
            if ($toAllWorkers)
            {
                foreach ((array) $flag as $tmpFlag)
                {
                    if ($this->sendAmqpMessage('closeByFlag', [
                        'messageId'  => bin2hex(random_bytes(16)),
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
                defer(fn () => imigo(function (): void {
                    try
                    {
                        $this->consumerInstance->run();
                    }
                    catch (\Throwable $th)
                    {
                        Log::error($th);
                        sleep(1);
                        $this->startSubscribe();
                    }
                }));
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
            return RequestContext::use(function (\ArrayObject $context) {
                $key = static::class . ':' . $this->server->getName() . ':publisherClass';

                return $context[$key] ?? ($context[$key] = $this->server->getBean($this->publisherClass, $this));
            });
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
