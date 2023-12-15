<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\PDOMysql;

use Imi\Bean\Annotation\Bean;
use Imi\Db\ConnectionCenter\DatabaseDriverConfig;
use Imi\Db\Drivers\PDO\TPDODriver;
use Imi\Db\Mysql\Drivers\MysqlBase;
use Imi\Db\Mysql\Util\SqlUtil;

/**
 * PDO MySQL驱动.
 */
#[Bean(name: 'PDOMysqlDriver')]
class Driver extends MysqlBase
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

        return $config->dsn ?? 'mysql:'
                 . 'host=' . $config->host
                 . ';port=' . ($config->port ?? self::DEFAULT_PORT)
                 . ';dbname=' . ($config->database ?? '')
                 . ';unix_socket=' . ($config->option['unix_socket'] ?? '')
                 . ';charset=' . ($config->charset ?? self::DEFAULT_CHARSET)
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
