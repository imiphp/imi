<?php

declare(strict_types=1);

namespace Imi;

use Imi\Event\Event;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\CoServer;

class ServerManage
{
    /**
     * 服务器对象数组.
     *
     * @var array
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
     * @return \Imi\Swoole\Server\Base[]
     */
    public static function getServers(): array
    {
        return static::$servers;
    }

    /**
     * 获取服务器对象
     *
     * @param string $name
     *
     * @return \Imi\Swoole\Server\Base|null
     */
    public static function getServer(string $name): ?Base
    {
        return static::$servers[$name] ?? null;
    }

    /**
     * 创建服务器对象
     *
     * @param string $name
     * @param array  $config
     * @param bool   $subServer 是否为子服务器
     *
     * @return \Imi\Swoole\Server\Base
     */
    public static function createServer(string $name, array $config, bool $isSubServer = false): \Imi\Swoole\Server\Base
    {
        // 创建服务器对象前置操作
        Event::trigger('IMI.SERVER.CREATE.BEFORE', [
            'name'          => $name,
            'config'        => $config,
            'isSubServer'   => $isSubServer,
        ]);
        // 主服务器实例对象
        $server = App::getBean($config['type'], $name, $config, $isSubServer);
        static::$servers[$name] = $server;
        // 创建服务器对象后置操作
        Event::trigger('IMI.SERVER.CREATE.AFTER', [
            'name'          => $name,
            'config'        => $config,
            'isSubServer'   => $isSubServer,
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
