<?php

declare(strict_types=1);

namespace Imi\Db;

class SqlState
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 检查错误码是否为掉线
     */
    public static function checkCodeIsOffline(string $sqlState): bool
    {
        return \in_array($sqlState, [
            '08000', // connection_exception
            '08001', // sqlclient_unable_to_establish_sqlconnection
            '08003', // connection_does_not_exist
            '08004', // sqlserver_rejected_establishment_of_sqlconnection
            '08006', // connection_failure
            '08007', // transaction_resolution_unknown
            '08P01', // protocol_violation
            '57P01', // admin_shutdown
            '57P02', // crash_shutdown
            '57P03', // cannot_connect_now
        ]);
    }
}
