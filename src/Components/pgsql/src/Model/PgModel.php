<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model;

use Imi\Model\Contract\IModelQuery;
use Imi\Model\Model;

/**
 * Pgsql 模型.
 */
class PgModel extends Model
{
    public const DEFAULT_QUERY_CLASS = ModelQuery::class;

    /**
     * {@inheritDoc}
     */
    public static function query(?string $poolName = null, ?int $queryType = null, string $queryClass = self::DEFAULT_QUERY_CLASS): IModelQuery
    {
        return parent::query($poolName, $queryType, $queryClass);
    }

    /**
     * @param bool|int $timeAccuracy
     *
     * @return mixed
     */
    protected static function parseDateTime(?string $columnType, $timeAccuracy)
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
                return (int) (microtime(true) * (true === $timeAccuracy ? 1000 : $timeAccuracy));
            default:
                return null;
        }
    }
}
