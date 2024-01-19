<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\PDOPgsql;

use Imi\Bean\Annotation\Bean;
use Imi\Db\ConnectionCenter\DatabaseDriverConfig;
use Imi\Db\Drivers\PDO\TPDODriver;
use Imi\Pgsql\Db\PgsqlBase;
use Imi\Pgsql\Db\Util\SqlUtil;

/**
 * PDO Pgsql驱动.
 */
#[Bean(name: 'PDOPgsqlDriver')]
class Driver extends PgsqlBase
{
    use TPDODriver;

    public const DEFAULT_OPTIONS = [
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::ATTR_EMULATE_PREPARES  => false,
        \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
    ];

    public function __construct(DatabaseDriverConfig $config)
    {
        parent::__construct($config);
        $this->statementClass = Statement::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildDSN(): string
    {
        $config = $this->config;

        return $config->dsn ?? 'pgsql:'
                . 'host=' . $config->host
                . ';port=' . ($config->port ?? self::DEFAULT_PORT)
                . ';dbname=' . ($config->database ?? '')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function batchExec(string $sql): array
    {
        $result = [];
        foreach (SqlUtil::parseMultiSql($sql) as $itemSql)
        {
            $queryResult = $this->query($itemSql);
            $fetchResult = $queryResult->fetchAll();
            $result[] = [[]] === $fetchResult ? [] : $fetchResult;
        }

        return $result;
    }
}
