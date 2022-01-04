<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\App;
use Imi\Model\Enum\RedisStorageMode;
use Imi\Redis\RedisHandler;
use Imi\Redis\RedisManager;
use Imi\Util\Format\IFormat;

/**
 * Redis 模型.
 */
abstract class RedisModel extends BaseModel
{
    /**
     * 默认的key.
     */
    protected string $key = '';

    /**
     * 默认的member.
     */
    protected string $__member = '';

    /**
     * set时，设置的数据过期时间.
     */
    protected ?int $__ttl = null;

    public function __init(array $data = []): void
    {
        parent::__init($data);
        $this->__ttl = ModelManager::getRedisEntity($this)->ttl;
    }

    /**
     * 查找一条记录.
     *
     * @param string|array $condition
     *
     * @return static|null
     */
    public static function find($condition): ?self
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        $key = static::generateKey($condition);
        switch ($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                $data = static::__getRedis()->get($key);
                if ($data && null !== $redisEntity->formatter)
                {
                    /** @var IFormat $formatter */
                    $formatter = App::getBean($redisEntity->formatter);
                    $data = $formatter->decode($data);
                }
                break;
            case RedisStorageMode::HASH:
                $member = static::generateMember($condition);
                $data = static::__getRedis()->hGet($key, $member);
                if ($data && null !== $redisEntity->formatter)
                {
                    /** @var IFormat $formatter */
                    $formatter = App::getBean($redisEntity->formatter);
                    $data = $formatter->decode($data);
                }
                break;
            case RedisStorageMode::HASH_OBJECT:
                $data = static::__getRedis()->hGetAll($key);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
        if (!$data)
        {
            return null;
        }
        $record = static::createFromRecord($data);
        $record->key = $key;
        if (isset($member))
        {
            $record->__member = $member;
        }

        return $record;
    }

    /**
     * 查询多条记录.
     *
     * @param mixed $conditions
     *
     * @return static[]
     */
    public static function select(...$conditions): array
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        if (null !== $redisEntity->formatter)
        {
            /** @var IFormat $formatter */
            $formatter = App::getBean($redisEntity->formatter);
        }
        $keys = [];
        if ($conditions)
        {
            foreach ($conditions as $condition)
            {
                $keys[] = static::generateKey($condition);
            }
        }
        switch ($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                $datas = static::__getRedis()->mget($keys);
                $list = [];
                if ($datas)
                {
                    foreach ($datas as $i => $data)
                    {
                        if (null !== $data)
                        {
                            if (isset($formatter))
                            {
                                $data = $formatter->decode($data);
                            }
                            $record = static::createFromRecord($data);
                            $record->key = $keys[$i];
                            $list[] = $record;
                        }
                    }
                }

                return $list;
            case RedisStorageMode::HASH:
                $members = [];
                if ($conditions)
                {
                    foreach ($conditions as $condition)
                    {
                        $members[] = static::generateMember($condition);
                    }
                }
                $list = [];
                $redis = static::__getRedis();
                if ($keys)
                {
                    foreach (array_unique($keys) as $key)
                    {
                        $datas = $redis->hMget($key, $members);
                        if ($datas)
                        {
                            foreach ($datas as $i => $data)
                            {
                                if (null !== $data)
                                {
                                    if (isset($formatter))
                                    {
                                        $data = $formatter->decode($data);
                                    }
                                    $record = static::createFromRecord($data);
                                    $record->key = $key;
                                    if (isset($members[$i]))
                                    {
                                        $record->__member = $members[$i];
                                    }
                                    $list[] = $record;
                                }
                            }
                        }
                    }
                }

                return $list;
            case RedisStorageMode::HASH_OBJECT:
                $redis = static::__getRedis();
                $list = [];
                if ($keys)
                {
                    foreach ($keys as $key)
                    {
                        $data = $redis->hGetAll($key);
                        $record = static::createFromRecord($data);
                        $record->key = $key;
                        $list[] = $record;
                    }
                }

                return $list;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 保存记录.
     */
    public function save(): bool
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        $redis = static::__getRedis($this);
        switch ($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                if (null === $redisEntity->formatter)
                {
                    $data = $this->toArray();
                }
                else
                {
                    /** @var IFormat $formatter */
                    $formatter = App::getBean($redisEntity->formatter);
                    $data = $formatter->encode($this->toArray());
                }
                if (null === $this->__ttl)
                {
                    return $redis->set($this->__getKey(), $data);
                }
                else
                {
                    return $redis->set($this->__getKey(), $data, $this->__ttl);
                }
                // no break
            case RedisStorageMode::HASH:
                if (null === $redisEntity->formatter)
                {
                    $data = $this->toArray();
                }
                else
                {
                    /** @var IFormat $formatter */
                    $formatter = App::getBean($redisEntity->formatter);
                    $data = $formatter->encode($this->toArray());
                }

                return false !== $redis->hSet($this->__getKey(), $this->__getMember(), $data);
            case RedisStorageMode::HASH_OBJECT:
                $key = $this->__getKey();
                $result = $redis->hMset($key, $this->toArray());
                if ($result && null !== $this->__ttl)
                {
                    $result = $redis->expire($key, $this->__ttl);
                }

                return $result;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 删除记录.
     */
    public function delete(): bool
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        switch ($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                return static::__getRedis($this)->del($this->__getKey()) > 0;
            case RedisStorageMode::HASH:
                return static::__getRedis($this)->hDel($this->__getKey(), $this->__getMember()) > 0;
            case RedisStorageMode::HASH_OBJECT:
                return static::__getRedis($this)->del($this->__getKey()) > 0;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 批量删除.
     *
     * @param mixed ...$conditions
     */
    public static function deleteBatch(...$conditions): int
    {
        if (!$conditions)
        {
            return 0;
        }
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        switch ($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                $keys = [];
                foreach ($conditions as $condition)
                {
                    $keys[] = static::generateKey($condition);
                }

                return static::__getRedis()->del(...$keys) ?: 0;
            case RedisStorageMode::HASH:
                $result = 0;
                foreach ($conditions as $condition)
                {
                    $key = static::generateKey($condition);
                    $member = static::generateMember($condition);
                    $result += (static::__getRedis()->hDel($key, $member) ?: 0);
                }

                return $result;
            case RedisStorageMode::HASH_OBJECT:
                $keys = [];
                foreach ($conditions as $condition)
                {
                    $keys[] = static::generateKey($condition);
                }

                return static::__getRedis()->del(...$keys) ?: 0;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 获取键.
     */
    public function __getKey(): string
    {
        $rule = ModelManager::getKeyRule($this);
        $replaces = [];
        foreach ($rule->paramNames as $paramName)
        {
            if (!isset($this[$paramName]))
            {
                throw new \RuntimeException(sprintf('__getKey param %s does not exists', $paramName));
            }
            $replaces['{' . $paramName . '}'] = $this[$paramName];
        }

        return strtr($rule->rule, $replaces);
    }

    /**
     * 生成key.
     *
     * @param string|array $condition
     */
    public static function generateKey($condition): string
    {
        if (\is_string($condition))
        {
            return $condition;
        }
        else
        {
            $rule = ModelManager::getKeyRule(static::class);
            $replaces = [];
            foreach ($rule->paramNames as $paramName)
            {
                if (!isset($condition[$paramName]))
                {
                    throw new \RuntimeException(sprintf('GenerateKey param %s does not exists', $paramName));
                }
                $replaces['{' . $paramName . '}'] = $condition[$paramName];
            }

            return strtr($rule->rule, $replaces);
        }
    }

    /**
     * 生成member.
     *
     * @param string|array $condition
     */
    public static function generateMember($condition): string
    {
        if (\is_string($condition))
        {
            return $condition;
        }
        else
        {
            $rule = ModelManager::getMemberRule(static::class);
            $replaces = [];
            foreach ($rule->paramNames as $paramName)
            {
                if (!isset($condition[$paramName]))
                {
                    throw new \RuntimeException(sprintf('GenerateMember param %s does not exists', $paramName));
                }
                $replaces['{' . $paramName . '}'] = $condition[$paramName];
            }

            return strtr($rule->rule, $replaces);
        }
    }

    /**
     * 获取键.
     */
    public function __getMember(): string
    {
        $rule = ModelManager::getMemberRule($this);
        $replaces = [];
        foreach ($rule->paramNames as $paramName)
        {
            if (!isset($this[$paramName]))
            {
                throw new \RuntimeException(sprintf('__getMember param %s does not exists', $paramName));
            }
            $replaces['{' . $paramName . '}'] = $this[$paramName];
        }

        return strtr($rule->rule, $replaces);
    }

    /**
     * 获取Redis操作对象
     *
     * @param static|null $redisModel
     */
    public static function __getRedis(?self $redisModel = null): RedisHandler
    {
        $annotation = ModelManager::getRedisEntity($redisModel ?? static::class);
        $redis = RedisManager::getInstance($annotation->poolName);
        if (null !== $annotation->db)
        {
            $redis->select($annotation->db);
        }

        return $redis;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get 默认的member.
     */
    public function getMember(): string
    {
        return $this->__member;
    }

    /**
     * Set 默认的member.
     *
     * @param string $member 默认的member
     */
    public function setMember(string $member): self
    {
        $this->__member = $member;

        return $this;
    }
}
