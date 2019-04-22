<?php
namespace Imi;

use Imi\Util\Imi;
use Imi\Main\Helper;
use Imi\Util\ArrayData;

abstract class Config
{
    /**
     * 配置数组
     * @var ArrayData[]
     */
    private static $configs = [];

    /**
     * 增加配置
     * @param string $name
     * @param array $config
     * @return boolean
     */
    public static function addConfig($name, array $config)
    {
        $nameSplit = explode('.', $name);

        $first = array_shift($nameSplit);
        if(!isset(static::$configs[$first]))
        {
            static::$configs[$first] = new ArrayData([]);
        }

        if(isset($nameSplit[0]))
        {
            $configName = implode('.', $nameSplit);
            static::$configs[$first]->set($configName, $config);
            if(false !== ($configs = static::$configs[$first]->get($configName . '.configs')))
            {
                static::load($name, $configs);
            }
        }
        else
        {
            static::$configs[$first]->set($config);
            if(static::$configs[$first]->exists('configs'))
            {
                static::load($name, static::$configs[$first]->get('configs', []));
            }
        }

        return true;
    }

    /**
     * 加载配置列表
     * @param array $configList
     * @return void
     */
    public static function load($name, array $configList)
    {
        foreach($configList as $alias => $fileName)
        {
            static::set($name . '.' . $alias, include $fileName);
        }
    }
    
    /**
     * 设置配置
     * @param string $name
     * @param array $config
     * @return boolean
     */
    public static function setConfig($name, array $config)
    {
        static::$configs[$name] = new ArrayData($config);
    }

    /**
     * 移除配置项
     * @param string $name
     * @return boolean
     */
    public static function removeConfig($name)
    {
        if(isset(static::$configs[$name]))
        {
            unset(static::$configs[$name]);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 设置配置值
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public static function set(string $name, $value)
    {
        $names = Imi::parseDotRule($name);
        if (isset($names[0]))
        {
            $first = array_shift($names);
            if(isset(static::$configs[$first]))
            {
                return static::$configs[$first]->setVal($names, $value);
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
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $names = Imi::parseDotRule($name);
        if (isset($names[0]))
        {
            $first = array_shift($names);
            if('@currentServer' === $first)
            {
                if($isCurrentServer = (RequestContext::exists() && null !== ($server = RequestContext::getServer())))
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
            if(isset(static::$configs[$first]))
            {
                $result = static::$configs[$first]->get($names, null);
            }
            if(isset($result))
            {
                return $result;
            }
            else if($isCurrentServer)
            {
                $first = '@app';
                unset($names[0]);
                return static::$configs[$first]->get($names, $default);
            }
        }
        return $default;
    }

    /**
     * 配置值是否存在
     * @param string $name
     * @return boolean
     */
    public static function has(string $name)
    {
        $names = Imi::parseDotRule($name);
        if (isset($names[0]))
        {
            $first = array_shift($names);
            if(isset(static::$configs[$first]))
            {
                return null !== static::$configs[$first]->get($names, null);
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
     * 获取所有别名
     * @return array
     */
    public static function getAlias()
    {
        return array_keys(static::$configs);
    }

    /**
     * 清空所有配置项
     * @return void
     */
    public static function clear()
    {
        static::$configs = [];
    }
}