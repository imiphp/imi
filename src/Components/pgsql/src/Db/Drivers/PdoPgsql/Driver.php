<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\PdoPgsql;

use Imi\Bean\Annotation\Bean;
use Imi\Db\Drivers\TPdoDriver;
use Imi\Pgsql\Db\PgsqlBase;
use Imi\Pgsql\Db\Util\SqlUtil;

/**
 * PDO Pgsql驱动.
 *
 * @Bean("PdoPgsqlDriver")
 */
class Driver extends PgsqlBase
{
    use TPdoDriver {
        __construct as private tPdoDriverConstruct;
    }

    public function __construct(array $option = [])
    {
        $option['username'] ??= 'postgres';
        $this->tPdoDriverConstruct($option);
        $this->statementClass = $option['statementClass'] ?? Statement::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildDSN(): string
    {
        $option = $this->option;
        if (isset($option['dsn']))
        {
            return $option['dsn'];
        }

        return 'pgsql:'
                 . 'host=' . ($option['host'] ?? '127.0.0.1')
                 . ';port=' . ($option['port'] ?? '5432')
                 . ';dbname=' . ($option['database'] ?? 'database')
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
