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

    private static array $dotRulesCache = [];

    private function __construct()
    {
    }

    /**
     * 增加配置.
     */
    public static function addConfig(string $name, array $config): bool
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

        if ($nameSplit)
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
     */
    public static function load(string $name, array $configList): void
    {
        if ($configList)
        {
            foreach ($configList as $alias => $fileName)
            {
                static::set($name . '.' . $alias, include $fileName);
            }
        }
    }

    /**
     * 设置配置.
     */
    public static function setConfig(string $name, array $config): void
    {
        static::$configs[$name] = new ArrayData($config);
    }

    /**
     * 移除配置项.
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
     * @param mixed $value
     */
    public static function set(string $name, $value): bool
    {
        if ('cli' === \PHP_SAPI)
        {
            $names = Imi::parseDotRule($name);
        }
        else
        {
            if (isset(self::$dotRulesCache[$name]))
            {
                $names = self::$dotRulesCache[$name];
            }
            else
            {
                $names = self::$dotRulesCache[$name] = Imi::parseDotRule($name);
            }
        }
        if ($names)
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
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        if ('cli' === \PHP_SAPI)
        {
            $names = Imi::parseDotRule($name);
        }
        else
        {
            if (isset(self::$dotRulesCache[$name]))
            {
                $names = self::$dotRulesCache[$name];
            }
            else
            {
                $names = self::$dotRulesCache[$name] = Imi::parseDotRule($name);
            }
        }
        if ($names)
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
                // @phpstan-ignore-next-line
                if ($names)
                {
                    $result = $configs[$first]->get($names, null);
                }
                else
                {
                    $result = $configs[$first]->getRawData();
                }
            }
            if (isset($result))
            {
                return $result;
            }
            elseif ($isCurrentServer)
            {
                $first = '@app';
                unset($names[0]);

                // @phpstan-ignore-next-line
                if ($names)
                {
                    return $configs[$first]->get($names, $default);
                }
                else
                {
                    return $configs[$first]->getRawData();
                }
            }
        }

        return $default;
    }

    /**
     * 配置值是否存在.
     */
    public static function has(string $name): bool
    {
        if ('cli' === \PHP_SAPI)
        {
            $names = Imi::parseDotRule($name);
        }
        else
        {
            if (isset(self::$dotRulesCache[$name]))
            {
                $names = self::$dotRulesCache[$name];
            }
            else
            {
                $names = self::$dotRulesCache[$name] = Imi::parseDotRule($name);
            }
        }
        if ($names)
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
     */
    public static function getAliases(): array
    {
        return array_keys(static::$configs);
    }

    /**
     * 清空所有配置项.
     */
    public static function clear(): void
    {
        static::$configs = [];
    }
}
