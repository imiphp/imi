<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers;

use Imi\Db\Drivers\BaseDbConnection;
use Imi\Db\Mysql\Contract\IMysqlDb;
use Imi\Db\Mysql\Query\MysqlQuery;
use Imi\Db\Query\Interfaces\IQuery;

abstract class MysqlBase extends BaseDbConnection implements IMysqlDb
{
    public const DEFAULT_PORT = 3306;

    public const DEFAULT_USERNAME = 'root';

    public const DEFAULT_PASSWORD = '';

    public const DEFAULT_CHARSET = 'utf8mb4';

    /**
     * {@inheritDoc}
     */
    public function createQuery(?string $modelClass = null): IQuery
    {
        return MysqlQuery::newInstance($this, $modelClass, null, null);
    }

    /**
     * {@inheritDoc}
     */
    public function getDbType(): string
    {
        return 'MySQL';
    }

    /**
     * {@inheritDoc}
     *
     * @see https://github.com/mysql/mysql-server/blob/HEAD/include/errmsg.h
     */
    public function checkCodeIsOffline(mixed $code): bool
    {
        return \in_array($code, [
            2001, // CR_SOCKET_CREATE_ERROR
            2002, // CR_CONNECTION_ERROR
            2003, // CR_CONN_HOST_ERROR
            2004, // CR_IPSOCK_ERROR
            2005, // CR_UNKNOWN_HOST
            2006, // CR_SERVER_GONE_ERROR
            2009, // CR_WRONG_HOST_INFO
            2012, // CR_SERVER_HANDSHAKE_ERR
            2013, // CR_SERVER_LOST
            2026, // CR_SSL_CONNECTION_ERROR
        ]);
    }
}
