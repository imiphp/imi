<?php

namespace Imi;

use Imi\Event\Event;
use Imi\Server\CoServer;

abstract class ServerManage
{
    /**
     * 服务器对象数组.
     *
     * @var array
     */
    private static $servers = [];

    /**
     * 协程服务器.
     *
     * @var \Imi\Server\CoServer
     */
    private static $coServer;

    /**
     * 获取服务器数组.
     *
     * @return \Imi\Server\Base[]
     */
    public static function getServers()
    {
        return static::$servers;
    }

    /**
     * 获取服务器对象
     *
     * @param string $name
     *
     * @return \Imi\Server\Base|null
     */
    public static function getServer($name)
    {
        return static::$servers[$name] ?? null;
    }

    /**
     * 创建服务器对象
     *
     * @param string $name
     * @param array  $config
     * @param bool   $isSubServer 是否为子服务器
     *
     * @return \Imi\Server\Base
     */
    public static function createServer($name, $config, $isSubServer = false): \Imi\Server\Base
    {
        // 创建服务器对象前置操作
        Event::trigger('IMI.SERVER.CREATE.BEFORE', [
            'name'          => $name,
            'config'        => $config,
            'isSubServer'   => $isSubServer,
        ]);
        // 服务器类名
        $serverClassName = 'Imi\Server\\' . $config['type'] . '\Server';
        // 主服务器实例对象
        $server = App::getBean($serverClassName, $name, $config, $isSubServer);
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
     * @return \Imi\Server\CoServer
     */
    public static function createCoServer($name, $workerNum): CoServer
    {
        return static::$coServer = new CoServer($name, $workerNum);
    }

    /**
     * 获取协程服务器.
     *
     * @return \Imi\Server\CoServer
     */
    public static function getCoServer(): CoServer
    {
        return static::$coServer;
    }
}
