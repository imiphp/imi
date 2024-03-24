<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db;

use Imi\Db\Drivers\Base;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\SqlState;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Query\PgsqlQuery;

abstract class PgsqlBase extends Base implements IPgsqlDb
{
    /**
     * {@inheritDoc}
     */
    public function createQuery(?string $modelClass = null): IQuery
    {
        return PgsqlQuery::newInstance($this, $modelClass, null, null);
    }

    /**
     * {@inheritDoc}
     */
    public function getDbType(): string
    {
        return 'PostgreSQL';
    }

    /**
     * {@inheritDoc}
     */
    public function checkCodeIsOffline(string $sqlState, $code = null): bool
    {
        return SqlState::checkCodeIsOffline($sqlState);
    }
}
