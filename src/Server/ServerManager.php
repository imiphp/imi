<?php

declare(strict_types=1);

namespace Imi\Server;

use Imi\App;
use Imi\Event\Event;
use Imi\Server\Contract\IServer;

class ServerManager
{
    /**
     * 服务器对象数组.
     *
     * @var \Imi\Server\Contract\IServer[]
     */
    private static array $servers = [];

    private function __construct()
    {
    }

    /**
     * 获取服务器数组.
     *
     * @return \Imi\Server\Contract\IServer[]
     */
    public static function getServers(?string $class = null): array
    {
        if (null === $class)
        {
            return self::$servers;
        }
        else
        {
            $servers = self::$servers;
            foreach ($servers as $name => $server)
            {
                if (!$server instanceof $class)
                {
                    unset($servers[$name]);
                }
            }

            return $servers;
        }
    }

    /**
     * 获取服务器对象
     */
    public static function getServer(string $name, ?string $class = null): ?IServer
    {
        $server = self::$servers[$name] ?? null;
        if (null === $class || $server instanceof $class)
        {
            return $server;
        }

        return null;
    }

    /**
     * 创建服务器对象
     *
     * @param mixed $args
     */
    public static function createServer(string $name, array $config, ...$args): IServer
    {
        // 创建服务器对象前置操作
        Event::trigger('IMI.SERVER.CREATE.BEFORE', [
            'name'   => $name,
            'config' => $config,
            'args'   => $args,
        ]);
        // 主服务器实例对象
        $server = self::$servers[$name] = App::newInstance($config['type'], $name, $config, ...$args);
        // 创建服务器对象后置操作
        Event::trigger('IMI.SERVER.CREATE.AFTER', [
            'name'   => $name,
            'config' => $config,
            'args'   => $args,
        ]);

        return $server;
    }
}
