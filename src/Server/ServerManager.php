<?php

declare(strict_types=1);

namespace Imi\Server;

use Imi\App;
use Imi\Event\Event;
use Imi\Server\Contract\IServer;
use Imi\Swoole\Server\CoServer;

class ServerManager
{
    /**
     * 服务器对象数组.
     *
     * @var \Imi\Server\Contract\IServer[]
     */
    private static array $servers = [];

    /**
     * 协程服务器.
     *
     * @var \Imi\Swoole\Server\CoServer
     */
    private static CoServer $coServer;

    private function __construct()
    {
    }

    /**
     * 获取服务器数组.
     *
     * @param string|null $class
     *
     * @return \Imi\Server\Contract\IServer[]
     */
    public static function getServers(?string $class = null): array
    {
        if (null === $class)
        {
            return static::$servers;
        }
        else
        {
            $servers = static::$servers;
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
     *
     * @param string      $name
     * @param string|null $class
     *
     * @return \Imi\Server\Contract\IServer|null
     */
    public static function getServer(string $name, ?string $class = null): ?IServer
    {
        $server = static::$servers[$name] ?? null;
        if (null === $class || $server instanceof $class)
        {
            return $server;
        }

        return null;
    }

    /**
     * 创建服务器对象
     *
     * @param string $name
     * @param array  $config
     * @param mixed  $args
     *
     * @return \Imi\Server\Contract\IServer
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
        $server = static::$servers[$name] = App::getBean($config['type'], $name, $config, ...$args);
        // 创建服务器对象后置操作
        Event::trigger('IMI.SERVER.CREATE.AFTER', [
            'name'   => $name,
            'config' => $config,
            'args'   => $args,
        ]);

        return $server;
    }

    /**
     * 创建协程服务器.
     *
     * @param string $name
     * @param int    $workerNum
     *
     * @return \Imi\Swoole\Server\CoServer
     */
    public static function createCoServer(string $name, int $workerNum): CoServer
    {
        return static::$coServer = new CoServer($name, $workerNum);
    }

    /**
     * 获取协程服务器.
     *
     * @return \Imi\Swoole\Server\CoServer
     */
    public static function getCoServer(): CoServer
    {
        return static::$coServer;
    }
}
