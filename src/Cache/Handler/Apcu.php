<?php

declare(strict_types=1);

namespace Imi\Cache\Handler;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("ApcuCache")
 */
class Apcu extends Base
{
    /**
     * 缓存键前缀
     */
    protected string $prefix = '';

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $result = apcu_fetch($this->parseKey($key), $success);
        if ($success)
        {
            return $result;
        }
        else
        {
            return $default;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null)
    {
        return apcu_store($this->parseKey($key), $value, (int) $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {
        return apcu_delete($this->parseKey($key));
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key)
        {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $newValues = [];
        foreach ($values as $k => $v)
        {
            $newValues[$this->parseKey($k)] = $v;
        }

        return [] === apcu_store($newValues, null, (int) $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple($keys)
    {
        $newKeys = [];
        foreach ($keys as $key)
        {
            $newKeys[] = $this->parseKey($key);
        }

        return [] === apcu_delete($newKeys);
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        return apcu_exists($this->parseKey($key));
    }

    /**
     * 处理键.
     */
    public function parseKey(string $key): string
    {
        return $this->prefix . $key;
    }
}
