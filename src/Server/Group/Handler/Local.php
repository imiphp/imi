<?php

declare(strict_types=1);

namespace Imi\Server\Group\Handler;

use Imi\Bean\Annotation\Bean;

/**
 * 分组本地驱动.
 *
 * @Bean("GroupLocal")
 */
class Local implements IGroupHandler
{
    /**
     * 组配置.
     */
    private array $groups = [];

    /**
     * {@inheritDoc}
     */
    public function startup(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function hasGroup(string $groupName): bool
    {
        return isset($this->groups[$groupName]);
    }

    /**
     * {@inheritDoc}
     */
    public function createGroup(string $groupName, int $maxClients = -1): void
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $groups[$groupName] = [
                'maxClient'       => $maxClients,
                'clientIds'       => [],
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeGroup(string $groupName): void
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            unset($groups[$groupName]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function joinGroup(string $groupName, $clientId): bool
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $this->createGroup($groupName);
        }
        $groups[$groupName]['clientIds'][$clientId] = $clientId;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function leaveGroup(string $groupName, $clientId): bool
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            $clientIds = &$groups[$groupName]['clientIds'];
            if (isset($clientIds[$clientId]))
            {
                unset($clientIds[$clientId]);

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isInGroup(string $groupName, $clientId): bool
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            return isset($groups[$groupName]['clientIds'][$clientId]);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIds(string $groupName): array
    {
        return array_values($this->groups[$groupName]['clientIds'] ?? []);
    }

    /**
     * {@inheritDoc}
     */
    public function count(string $groupName): int
    {
        return \count($this->groups[$groupName]['clientIds'] ?? []);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->groups = [];
    }
}
