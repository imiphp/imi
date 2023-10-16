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
    public function get(string $key, mixed $default = null): mixed
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
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        return apcu_store($this->parseKey($key), $value, (int) $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        return apcu_delete($this->parseKey($key));
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
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
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $newValues = [];
        foreach ($values as $k => $v)
        {
            $newValues[$this->parseKey((string) $k)] = $v;
        }

        return [] === apcu_store($newValues, null, (int) $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple(iterable $keys): bool
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
    public function has(string $key): bool
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
