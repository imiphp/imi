<?php

namespace Imi\Cli;

use Imi\App;
use Imi\Bean\Annotation;
use Imi\Cache\CacheManager;
use Imi\Config;
use Imi\Main\Helper;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;

abstract class Tool
{
    private static $toolName;
    private static $toolOperation;

    /**
     * 获取当前命令行工具名称.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getToolName()
    {
        return static::$toolName;
    }

    /**
     * 获取当前命令行工具操作名称.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getToolOperation()
    {
        return static::$toolOperation;
    }

    /**
     * 初始化.
     *
     * @return void
     */
    public static function init()
    {
        // 跳过初始化的工具
        foreach (Config::get('@Imi.skipInitTools') as $tool)
        {
            if (static::$toolName === $tool[0] && static::$toolOperation === $tool[1])
            {
                return;
            }
        }

        // 仅初始化项目及组件
        $initMains = [Helper::getMain(App::getNamespace())];
        foreach (Helper::getAppMains() as $main)
        {
            foreach ($main->getConfig()['components'] ?? [] as $namespace)
            {
                $componentMain = Helper::getMain($namespace);
                if (null !== $componentMain)
                {
                    $initMains[] = $componentMain;
                }
            }
        }
        Annotation::getInstance()->init($initMains);

        // 获取配置
        $pools = $caches = [];
        foreach (Helper::getMains() as $main)
        {
            $pools = array_merge($pools, $main->getConfig()['pools'] ?? []);
            $caches = array_merge($caches, $main->getConfig()['caches'] ?? []);
        }
        // 同步池子初始化
        foreach ($pools as $name => $pool)
        {
            if (isset($pool['sync']))
            {
                $pool = $pool['sync'];
                $poolPool = $pool['pool'];
                PoolManager::addName($name, $poolPool['class'], new PoolConfig($poolPool['config']), $pool['resource']);
            }
            elseif (isset($pool['pool']['syncClass']))
            {
                $poolPool = $pool['pool'];
                PoolManager::addName($name, $poolPool['syncClass'], new PoolConfig($poolPool['config']), $pool['resource']);
            }
        }
        // 缓存初始化
        foreach ($caches as $name => $cache)
        {
            CacheManager::addName($name, $cache['handlerClass'], $cache['option'] ?? []);
        }
    }
}
