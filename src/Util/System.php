<?php

namespace Imi\Util;

use function function_exists;

class System
{
    /**
     * 获取本地 IP 列表
     */
    public static function netLocalIp(): array
    {
        $output = [];

        if (function_exists('\net_get_interfaces'))
        {
            foreach (net_get_interfaces() ?: [] as $name => $item)
            {
                $ip = $item['unicast'][1]['address'] ?? null;
                if (null === $ip || '127.0.0.1' === $ip)
                {
                    continue;
                }
                if ('Windows' === \PHP_OS_FAMILY && isset($item['description']))
                {
                    $name = $item['description'];
                }
                $output[$name] = $ip;
            }
        }
        else if(function_exists('\swoole_get_local_ip'))
        {
            $output = swoole_get_local_ip();
        }

        return $output;
    }
}
