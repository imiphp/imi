<?php

declare(strict_types=1);

namespace Imi;

use Imi\Util\ArrayData;
use Imi\Util\Imi;

class Config
{
    /**
     * 配置数组.
     *
     * @var ArrayData[]
     */
    private static array $configs = [];

    private function __construct()
    {
    }

    /**
     * 增加配置.
     *
     * @param string $name
     * @param array  $config
     *
     * @return bool
     */
    public static function addConfig($name, array $config): bool
    {
        $nameSplit = explode('.', $name);

        $first = array_shift($nameSplit);
        if (isset(static::$configs[$first]))
        {
            $configData = static::$configs[$first];
        }
        else
        {
            static::$configs[$first] = $configData = new ArrayData([]);
        }

        if (isset($nameSplit[0]))
        {
            $configName = implode('.', $nameSplit);
            $configData->set($configName, $config);
            if (false !== ($configs = $configData->get($configName . '.configs')))
            {
                static::load($name, $configs);
            }
        }
        else
        {
            $configData->set($config);
            if ($configData->exists('configs'))
            {
                static::load($name, $configData->get('configs', []));
            }
        }

        return true;
    }

    /**
     * 加载配置列表.
     *
     * @param array $configList
     *
     * @return void
     */
    public static function load($name, array $configList)
    {
        foreach ($configList as $alias => $fileName)
        {
            static::set($name . '.' . $alias, include $fileName);
        }
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param array  $config
     *
     * @return void
     */
    public static function setConfig($name, array $config)
    {
        static::$configs[$name] = new ArrayData($config);
    }

    /**
     * 移除配置项.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function removeConfig(string $name): bool
    {
        $configs = &static::$configs;
        if (isset($configs[$name]))
        {
            unset($configs[$name]);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 设置配置值
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public static function set(string $name, $value): bool
    {
        $names = Imi::parseDotRule($name);
        if (isset($names[0]))
        {
            $first = array_shift($names);
            $configs = &static::$configs;
            if (isset($configs[$first]))
            {
                return $configs[$first]->setVal($names, $value);
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取配置值
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $names = Imi::parseDotRule($name);
        if (isset($names[0]))
        {
            $first = array_shift($names);
            if ('@currentServer' === $first)
            {
                $server = RequestContext::get('server');
                $isCurrentServer = null !== $server;
                if ($isCurrentServer)
                {
                    $first = '@server';
                    array_unshift($names, $server->getName());
                }
                else
                {
                    $first = '@app';
                }
            }
            else
            {
                $isCurrentServer = false;
            }
            $configs = &static::$configs;
            if (isset($configs[$first]))
            {
                $result = $configs[$first]->get($names, null);
            }
            if (isset($result))
            {
                return $result;
            }
            elseif ($isCurrentServer)
            {
                $first = '@app';
                unset($names[0]);

                return $configs[$first]->get($names, $default);
            }
        }

        return $default;
    }

    /**
     * 配置值是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool
    {
        $names = Imi::parseDotRule($name);
        if (isset($names[0]))
        {
            $first = array_shift($names);
            $configs = &static::$configs;
            if (isset($configs[$first]))
            {
                return null !== $configs[$first]->get($names, null);
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取所有别名.
     *
     * @return array
     */
    public static function getAliases(): array
    {
        return array_keys(static::$configs);
    }

    /**
     * 清空所有配置项.
     *
     * @return void
     */
    public static function clear()
    {
        static::$configs = [];
    }
}
