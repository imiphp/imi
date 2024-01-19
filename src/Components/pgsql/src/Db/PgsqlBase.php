<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db;

use Imi\Db\Drivers\BaseDbConnection;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Query\PgsqlQuery;

abstract class PgsqlBase extends BaseDbConnection implements IPgsqlDb
{
    public const DEFAULT_PORT = 5432;

    public const DEFAULT_USERNAME = 'postgres';

    public const DEFAULT_PASSWORD = '';

    public const DEFAULT_CHARSET = 'utf8';

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
     *
     * @see http://www.postgres.cn/docs/13/errcodes-appendix.html
     */
    public function checkCodeIsOffline(mixed $code): bool
    {
        return null === $code || '57P01' === $code;
    }
}
