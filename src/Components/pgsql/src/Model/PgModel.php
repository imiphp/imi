<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Model;

/**
 * PgSql 模型.
 */
class PgModel extends Model
{
    public const DEFAULT_QUERY_CLASS = ModelQuery::class;

    /**
     * 返回一个查询器.
     *
     * @param string|null $poolName  连接池名，为null则取默认
     * @param int|null    $queryType 查询类型；Imi\Db\Query\QueryType::READ/WRITE
     */
    public static function query(?string $poolName = null, ?int $queryType = null, string $queryClass = self::DEFAULT_QUERY_CLASS): IQuery
    {
        return parent::query($poolName, $queryType, $queryClass);
    }

    /**
     * @return mixed
     */
    protected static function parseDateTime(?string $columnType)
    {
        switch ($columnType)
        {
            case 'date':
                return date('Y-m-d');
            case 'time':
            case 'timetz':
                return date('H:i:s');
            case 'timestamp':
            case 'timestamptz':
                return date('Y-m-d H:i:s');
            case 'int':
            case 'int2':
            case 'int4':
                return time();
            case 'int8':
                return (int) (microtime(true) * 1000);
            default:
                return null;
        }
    }
}
