<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Model\Enum\RelationPoolName;

trait TRelation
{
    /**
     * @param int|string|null $poolName
     */
    protected static function parsePoolName($poolName, string $parentModel, string $relationModel): ?string
    {
        if (null === $poolName || \is_string($poolName))
        {
            return $poolName;
        }
        switch ($poolName)
        {
            case RelationPoolName::PARENT:
                return $parentModel::__getMeta()->getDbPoolName();
            case RelationPoolName::RELATION:
                return $relationModel::__getMeta()->getDbPoolName();
        }
        throw new \InvalidArgumentException(sprintf('Invalid poolName %s', $poolName));
    }
}
