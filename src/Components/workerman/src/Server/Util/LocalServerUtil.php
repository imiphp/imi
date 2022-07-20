<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Util;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\Server;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Contract\IWorkermanServerUtil;
use Workerman\Connection\TcpConnection;

/**
 * @Bean(name="LocalServerUtil", env="workerman")
 */
class LocalServerUtil implements IWorkermanServerUtil
{
    /**
     * {@inheritDoc}
     */
    public function sendMessage(string $action, array $data = [], $workerId = null, ?string $serverName = null): int
    {
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessageRaw(array $data, $workerId = null, ?string $serverName = null): int
    {
        throw new \RuntimeException('Unsupport operation');
    }

    /**
     * {@inheritDoc}
     */
    public function send($data, $clientId = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRaw($dataParser->encode($data, $serverName), $clientId, $server->getName(), $toAllWorkers);
    }

    /**
     * {@inheritDoc}
     */
    public function sendByFlag($data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }

        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawByFlag($dataParser->encode($data, $serverName), $flag, $serverName, $toAllWorkers);
    }

    /**
     * {@inheritDoc}
     */
    public function sendRaw(string $data, $clientId = null, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        $worker = $server->getWorker();
        if (null === $clientId)
        {
            $clientId = ConnectionContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
        }
        $success = 0;
        foreach ((array) $clientId as $tmpClientId)
        {
            /** @var TcpConnection|null $connection */
            $connection = $worker->connections[$tmpClientId] ?? null;
            if (null !== $connection && $connection->send($data, !isset($connection->websocketCurrentFrameLength)))
            {
                ++$success;
            }
        }

        return $success;
    }

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

            return $this->sendRaw($data, $clientIds, $serverName, false);
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

            return $this->sendRaw($data, $clientIds, $serverName, false);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sendToAll($data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawToAll($dataParser->encode($data, $serverName), $server->getName(), false);
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

        $count = 0;
        /** @var TcpConnection $connection */
        foreach ($server->getWorker()->connections as $connection)
        {
            if ($connection->send($data))
            {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function sendToGroup($groupName, $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server || !$server->isLongConnection())
        {
            return 0;
        }
        /** @var \Imi\Server\DataParser\DataParser $dataParser */
        $dataParser = $server->getBean(DataParser::class);

        return $this->sendRawToGroup($groupName, $dataParser->encode($data, $serverName), $server->getName());
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

        $count = 0;
        $groups = (array) $groupName;
        foreach ($groups as $tmpGroupName)
        {
            $group = $server->getGroup($tmpGroupName);
            if ($group)
            {
                $count += $this->sendRaw($data, $group->getClientIds(), $serverName, false);
            }
        }

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function close($clientId, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        $server = $this->getServer($serverName);
        if (!$server)
        {
            return 0;
        }
        $worker = $server->getWorker();
        $count = 0;
        if (null === $clientId)
        {
            $clientId = ConnectionContext::getClientId();
            if (!$clientId)
            {
                return 0;
            }
            $clientIds = [(int) $clientId];
        }
        else
        {
            $clientIds = (array) $clientId;
        }
        foreach ($clientIds as $currentClientId)
        {
            /** @var TcpConnection|null $connection */
            $connection = $worker->connections[$currentClientId] ?? null;
            if ($connection)
            {
                $connection->close();
                ++$count;
            }
        }

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        if (null === $flag)
        {
            return $this->close(null, $serverName, false);
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

        return $this->close($clientIds, $serverName, false);
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

    /**
     * {@inheritDoc}
     */
    public function getConnections(?string $serverName = null): array
    {
        return $this->getServer($serverName)->getWorker()->connections;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectionCount(?string $serverName = null): int
    {
        return \count($this->getServer($serverName)->getWorker()->connections);
    }

    /**
     * {@inheritDoc}
     */
    public function getServer(?string $serverName = null): ?IWorkermanServer
    {
        return Server::getServer($serverName, IWorkermanServer::class);
    }
}
