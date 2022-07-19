<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Channel\Client;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Worker;

/**
 * @Bean("ChannelServerUtil")
 */
class ChannelServerUtil extends AbstractDistributedServerUtil
{
    /**
     * {@inheritDoc}
     */
    public function sendMessage(string $action, array $data = [], $workerId = null, ?string $serverName = null): int
    {
        $data['action'] = $action;

        return $this->sendMessageRaw($data, $workerId, $serverName);
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessageRaw(array $data, $workerId = null, ?string $serverName = null): int
    {
        if (null === $workerId)
        {
            $workerId = range(0, Worker::getWorkerNum() - 1);
        }
        $eventName = 'imi.process.message.' . (null === $serverName ? ($serverName . '.') : '');
        $success = 0;
        $currentWorkerId = Worker::getWorkerId();
        $server = RequestContext::getServer();
        $currentServerName = $server ? $server->getName() : null;
        foreach ((array) $workerId as $tmpWorkerId)
        {
            if ($tmpWorkerId === $currentWorkerId && (null === $serverName || $currentServerName === $serverName))
            {
                $action = $data['action'] ?? null;
                if (!$action)
                {
                    continue;
                }
                Event::trigger('IMI.PIPE_MESSAGE.' . $action, $data);
                ++$success;
            }
            else
            {
                Client::publish($eventName . $tmpWorkerId, $data);
                ++$success;
            }
        }

        return $success;
    }
}
