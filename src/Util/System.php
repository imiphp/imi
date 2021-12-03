<?php

declare(strict_types=1);

namespace Imi\Util;

class System
{
    /**
     * 获取本地 IP 列表.
     */
    public static function netLocalIp(): array
    {
        $output = [];

        if (\function_exists('\net_get_interfaces'))
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
        elseif (\function_exists('\swoole_get_local_ip'))
        {
            $output = swoole_get_local_ip();
        }

        return $output;
    }

    public static function getCpuCoresNum(): int
    {
        if (\PHP_OS_FAMILY == 'Windows')
        {
            return (int) getenv('NUMBER_OF_PROCESSORS');
        }
        elseif (is_file('/proc/cpuinfo'))
        {
            return substr_count(file_get_contents('/proc/cpuinfo'), "\nprocessor") + 1;
        }

        return 0;
    }
}
