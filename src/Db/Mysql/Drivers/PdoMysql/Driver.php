<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\PdoMysql;

use Imi\Bean\Annotation\Bean;
use Imi\Db\Drivers\TPdoDriver;
use Imi\Db\Mysql\Drivers\MysqlBase;
use Imi\Db\Mysql\Util\SqlUtil;

/**
 * PDO MySQL驱动.
 *
 * @Bean("PdoMysqlDriver")
 */
class Driver extends MysqlBase
{
    use TPdoDriver {
        __construct as private tPdoDriverConstruct;
    }

    public function __construct(array $option = [])
    {
        $option['username'] ??= 'root';
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

        return 'mysql:'
                 . 'host=' . ($option['host'] ?? '127.0.0.1')
                 . ';port=' . ($option['port'] ?? '3306')
                 . ';dbname=' . ($option['database'] ?? '')
                 . ';unix_socket=' . ($option['unix_socket'] ?? '')
                 . ';charset=' . ($option['charset'] ?? 'utf8')
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
