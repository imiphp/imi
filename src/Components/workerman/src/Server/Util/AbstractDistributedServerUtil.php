<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Imi\ConnectionContext;

abstract class AbstractDistributedServerUtil extends LocalServerUtil
{
    /**
     * {@inheritDoc}
     */
    public function sendRawByFlag(string $data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }
        if (null === $flag)
        {
            $clientId = ConnectionContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [$clientId];

            return $this->sendRaw($data, $clientIds, $serverName);
        }
        else
        {
            return (int) ($this->sendMessage('sendRawByFlagRequest', [
                'data'         => $data,
                'flag'         => $flag,
                'serverName'   => $serverName,
                'toAllWorkers' => $toAllWorkers,
            ], null, $serverName) > 0);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }

        return (int) ($this->sendMessage('sendRawToAllRequest', [
            'data'         => $data,
            'serverName'   => $serverName,
            'toAllWorkers' => $toAllWorkers,
        ], null, $serverName) > 0);
    }

    /**
     * {@inheritDoc}
     */
    public function sendRawToGroup($groupName, string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        if (null === $serverName)
        {
            $serverName = $server->getName();
        }

        return (int) ($this->sendMessage('sendRawToGroupRequest', [
            'groupName'    => $groupName,
            'data'         => $data,
            'serverName'   => $serverName,
            'toAllWorkers' => $toAllWorkers,
        ], null, $serverName) > 0);
    }

    /**
     * {@inheritDoc}
     */
    public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        if (null === $flag)
        {
            return $this->close(null, $serverName, $toAllWorkers);
        }
        if (null === $serverName)
        {
            $server = $this->getServer($serverName);
            if (!$server)
            {
                return 0;
            }
            $serverName = $server->getName();
        }

        return (int) ($this->sendMessage('closeByFlagRequest', [
            'flag'         => $flag,
            'serverName'   => $serverName,
            'toAllWorkers' => $toAllWorkers,
        ], null, $serverName) > 0);
    }

    /**
     * {@inheritDoc}
     */
    public function exists($clientId, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        $server = $this->getServer($serverName);
        if (!$server)
        {
            return false;
        }
        $worker = $server->getWorker();
        if (null === $clientId)
        {
            $clientId = ConnectionContext::getClientId();
        }

        return isset($worker->connections[$clientId]);
    }

    /**
     * {@inheritDoc}
     */
    public function flagExists(?string $flag, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        if (null === $flag)
        {
            $clientIds = [ConnectionContext::getClientId()];
        }
        else
        {
            $clientIds = ConnectionContext::getClientIdByFlag($flag, $serverName);
        }
        if ($clientIds)
        {
            $server = $this->getServer($serverName);
            $worker = $server->getWorker();
            foreach ($clientIds as $clientId)
            {
                if (isset($worker->connections[$clientId]))
                {
                    return true;
                }
            }
        }

        return false;
    }
}
