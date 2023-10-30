<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model;

use Imi\Model\Model;

/**
 * Pgsql 模型.
 */
class PgModel extends Model
{
    public const DEFAULT_QUERY_CLASS = ModelQuery::class;

    protected static array $__fieldInitParsers = [
        'json'  => 'parseJsonInitValue',
        'jsonb' => 'parseJsonInitValue',
        'list'  => 'parseListInitValue',
        'set'   => 'parseSetInitValue',
    ];

    protected static array $__fieldSaveParsers = [
        'json'  => 'parseJsonSaveValue',
        'jsonb' => 'parseJsonSaveValue',
        'list'  => 'parseListSaveValue',
        'set'   => 'parseSetSaveValue',
    ];

    protected static array $_fieldParseNullTypes = [
        'json',
        'jsonb',
    ];

    /**
     * @param bool|int $timeAccuracy 推荐最大精度6位（微秒），部分系统能提供9位精度（纳秒）
     */
    protected static function parseDateTime(?string $columnType, bool|int $timeAccuracy, ?float $microTime = null): int|string|null
    {
        $microTime ??= microtime(true);

        switch ($columnType)
        {
            case 'date':
                return date('Y-m-d', (int) $microTime);
            case 'time':
            case 'timetz':
                if ($timeAccuracy >= 1000)
                {
                    $sec = (int) $microTime;
                    $usec = $microTime - $sec; // 获取小数部分

                    return date('H:i:s.', $sec) . (int) ($usec * $timeAccuracy);
                }
                else
                {
                    return date('H:i:s');
                }
                // no break
            case 'timestamp':
            case 'timestamptz':
                if ($timeAccuracy >= 1000)
                {
                    $sec = (int) $microTime;
                    $usec = $microTime - $sec; // 获取小数部分

                    return date('Y-m-d H:i:s.', $sec) . (int) ($usec * $timeAccuracy);
                }
                else
                {
                    return date('Y-m-d H:i:s');
                }
                // no break
            case 'int':
            case 'int2':
            case 'int4':
                return (int) $microTime;
            case 'int8':
                return (int) ($microTime * (true === $timeAccuracy ? 1000 : $timeAccuracy));
            default:
                return null;
        }
    }
}
