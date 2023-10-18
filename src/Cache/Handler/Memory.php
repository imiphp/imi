<?php

declare(strict_types=1);

namespace Imi\Cache\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\DateTime;
use Imi\Util\ExpiredStorage;

/**
 * @Bean("MemoryCache")
 */
class Memory extends Base
{
    protected static ?ExpiredStorage $storage = null;

    public function __construct(array $option = [])
    {
        if (null === self::$storage)
        {
            self::$storage = new ExpiredStorage();
        }
        parent::__construct($option);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return self::$storage->get($key, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        // ttl 支持 \DateInterval 格式
        if ($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        self::$storage->set($key, $value, (int) $ttl);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        self::$storage->unset($key);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        self::$storage->clear();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $this->checkArrayOrTraversable($keys);
        $object = self::$storage;
        $result = [];
        foreach ($keys as $key)
        {
            $result[$key] = $object->get($key, $default);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $this->checkArrayOrTraversable($values);
        // ttl 支持 \DateInterval 格式
        if ($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        $object = self::$storage;
        foreach ($values as $key => $value)
        {
            $object->set((string) $key, $value, $ttl ?? 0);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $this->checkArrayOrTraversable($keys);
        $object = self::$storage;
        foreach ($keys as $key)
        {
            $object->unset($key);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return self::$storage->isset($key);
    }
}
