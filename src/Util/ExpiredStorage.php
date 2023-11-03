<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 支持键值过期的存储对象
 */
class ExpiredStorage
{
    /**
     * @var ExpiredStorageItem[]
     */
    protected array $data = [];

    public function __construct(array $data = [])
    {
        if ($data)
        {
            foreach ($data as $key => $value)
            {
                $this->data[$key] = new ExpiredStorageItem($value);
            }
        }
    }

    public function set(string $key, mixed $value, float $ttl = 0): self
    {
        if (isset($this->data[$key]))
        {
            $item = $this->data[$key];
            $item->setValue($value);
            $item->setTTL($ttl);
        }
        else
        {
            $this->data[$key] = new ExpiredStorageItem($value, $ttl);
        }

        return $this;
    }

    public function get(string $key, mixed $default = null, ?ExpiredStorageItem &$item = null): mixed
    {
        if (isset($this->data[$key]))
        {
            $item = $this->data[$key];
            if (!$item->isExpired())
            {
                return $item->getValue();
            }
        }

        return $default;
    }

    public function unset(string $key): void
    {
        unset($this->data[$key]);
    }

    public function isset(string $key): bool
    {
        if (isset($this->data[$key]))
        {
            return !$this->data[$key]->isExpired();
        }

        return false;
    }

    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * @return ExpiredStorageItem[]
     */
    public function getItems(): array
    {
        return $this->data;
    }
}

final class ExpiredStorageItem
{
    /**
     * @var mixed
     */
    private $value;

    private float $ttl = 0;

    private float $lastModifyTime = 0;

    public function __construct(mixed $value, float $ttl = 0)
    {
        $this->setValue($value);
        $this->setTTL($ttl);
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        $this->lastModifyTime = microtime(true);

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setTTL(float $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getTTL(): float
    {
        return $this->ttl;
    }

    public function isExpired(): bool
    {
        return $this->ttl > 0 && microtime(true) - $this->lastModifyTime > $this->ttl;
    }

    public function getLastModifyTime(): float
    {
        return $this->lastModifyTime;
    }
}
