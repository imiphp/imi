<?php
namespace Imi\Model;

use Imi\Model\Enum\RedisStorageMode;
use Imi\Pool\PoolManager;
use Imi\Redis\RedisManager;
use Imi\Util\MemoryTableManager;

/**
 * Redis 模型
 */
abstract class RedisModel extends BaseModel
{
    /**
     * 默认的key
     * @var string
     */
    protected $key;

    /**
     * 默认的member
     * @var string
     */
    protected $__member;

    /**
     * set时，设置的数据过期时间
     * @var int
     */
    protected $__ttl;

    public function __init($data = [])
    {
        parent::__init($data);
        $this->__ttl = ModelManager::getRedisEntity($this)->ttl;
    }

    /**
     * 查找一条记录
     * @param string|array $condition
     * @return static
     */
    public static function find($condition)
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        $key = static::generateKey($condition);
        switch($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                $data = static::__getRedis()->get($key);
                break;
            case RedisStorageMode::HASH:
                $member = static::generateMember($condition);
                $data = static::__getRedis()->hGet($key, $member);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
        if(!$data)
        {
            return null;
        }
        $record = static::newInstance($data);
        $record->key = $key;
        if(isset($member))
        {
            $record->__member = $member;
        }
        return $record;
    }

    /**
     * 查询多条记录
     * @return static[]
     */
    public static function select(...$conditions)
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        $keys = [];
        foreach($conditions as $condition)
        {
            $keys[] = static::generateKey($condition);
        }
        switch($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                $datas = static::__getRedis()->mGet($keys);
                $list = [];
                foreach($datas as $i => $data)
                {
                    if(null !== $data)
                    {
                        $record = static::newInstance($data);
                        $record->key = $keys[$i];
                        $list[] = $record;
                    }
                }
                return $list;
            case RedisStorageMode::HASH:
                $members = [];
                foreach($conditions as $condition)
                {
                    $members[] = static::generateMember($condition);
                }
                $list = [];
                foreach(array_unique($keys) as $key)
                {
                    $datas = static::__getRedis()->hMget($key, $members);
                    foreach($datas as $i => $data)
                    {
                        if(null !== $data)
                        {
                            $record = static::newInstance($data);
                            $record->key = $key;
                            if(isset($members[$i]))
                            {
                                $record->__member = $members[$i];
                            }
                            $list[] = $record;
                        }
                    }
                }
                return $list;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 保存记录
     * @return bool
     */
    public function save()
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        $redis = static::__getRedis($this);
        switch($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                if(null === $this->__ttl)
                {
                    return $redis->set($this->__getKey(), $this->toArray());
                }
                else
                {
                    return $redis->set($this->__getKey(), $this->toArray(), $this->__ttl);
                }
                break;
            case RedisStorageMode::HASH:
                return false !== $redis->hSet($this->__getKey(), $this->__getMember(), $this->toArray());
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }
    
    /**
     * 删除记录
     * @return bool
     */
    public function delete()
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        switch($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                return static::__getRedis($this)->del($this->__getKey()) > 0;
            case RedisStorageMode::HASH:
                return static::__getRedis($this)->hDel($this->__getKey(), $this->__getMember()) > 0;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 批量删除
     * @param string ...$conditions
     * @return int
     */
    public static function deleteBatch(...$conditions)
    {
        /** @var \Imi\Model\Annotation\RedisEntity $redisEntity */
        $redisEntity = ModelManager::getRedisEntity(static::__getRealClassName());
        $keys = [];
        foreach($conditions as $condition)
        {
            $keys[] = static::generateKey($condition);
        }
        switch($redisEntity->storage)
        {
            case RedisStorageMode::STRING:
                return static::__getRedis()->del(...$keys);
            case RedisStorageMode::HASH:
                $members = [];
                foreach($conditions as $condition)
                {
                    $members[] = static::generateMember($condition);
                }
                $result = true;
                foreach(array_unique($keys) as $key)
                {
                    if(false === static::__getRedis()->hDel($key, ...$members))
                    {
                        $result = false;
                        break;
                    }
                }
                return $result;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid RedisEntity->storage %s', $redisEntity->storage));
        }
    }

    /**
     * 获取键
     * @return string
     */
    public function __getKey()
    {
        $rule = ModelManager::getKeyRule($this);
        $replaces = [];
        foreach($rule->paramNames as $paramName)
        {
            if(!isset($this[$paramName]))
            {
                throw new \RuntimeException(sprintf('__getKey param %s does not exists', $paramName));
            }
            $replaces['{' . $paramName . '}'] = $this[$paramName];
        }
        return strtr($rule->rule, $replaces);
    }

    /**
     * 生成key
     * @param string|array $condition
     * @return string
     */
    public static function generateKey($condition)
    {
        if(is_string($condition))
        {
            return $condition;
        }
        else
        {
            $rule = ModelManager::getKeyRule(static::class);
            $replaces = [];
            foreach($rule->paramNames as $paramName)
            {
                if(!isset($condition[$paramName]))
                {
                    throw new \RuntimeException(sprintf('GenerateKey param %s does not exists', $paramName));
                }
                $replaces['{' . $paramName . '}'] = $condition[$paramName];
            }
            return strtr($rule->rule, $replaces);
        }
    }

    /**
     * 生成member
     * @param string|array $condition
     * @return string
     */
    public static function generateMember($condition)
    {
        if(is_string($condition))
        {
            return $condition;
        }
        else
        {
            $rule = ModelManager::getMemberRule(static::class);
            $replaces = [];
            foreach($rule->paramNames as $paramName)
            {
                if(!isset($condition[$paramName]))
                {
                    throw new \RuntimeException(sprintf('GenerateMember param %s does not exists', $paramName));
                }
                $replaces['{' . $paramName . '}'] = $condition[$paramName];
            }
            return strtr($rule->rule, $replaces);
        }
    }

    /**
     * 获取键
     * @return string
     */
    public function __getMember()
    {
        $rule = ModelManager::getMemberRule($this);
        $replaces = [];
        foreach($rule->paramNames as $paramName)
        {
            if(!isset($this[$paramName]))
            {
                throw new \RuntimeException(sprintf('__getMember param %s does not exists', $paramName));
            }
            $replaces['{' . $paramName . '}'] = $this[$paramName];
        }
        return strtr($rule->rule, $replaces);
    }

    /**
     * 获取Redis操作对象
     * @param RedisModel $redisModel
     * @return \Imi\Redis\RedisHandler
     */
    public static function __getRedis(RedisModel $redisModel = null)
    {
        $annotation = ModelManager::getRedisEntity(null === $redisModel ? static::class : $redisModel);
        $redis = RedisManager::getInstance($annotation->poolName);
        if(null !== $annotation->db)
        {
            $redis->select($annotation->db);
        }
        return $redis;
    }

    /**
     * Get the value of key
     * @return string
     */ 
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the value of key
     * @param string $key
     * @return self
     */ 
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get 默认的member
     *
     * @return string
     */ 
    public function getMember()
    {
        return $this->__member;
    }

    /**
     * Set 默认的member
     *
     * @param string $member  默认的member
     *
     * @return self
     */ 
    public function setMember(string $member)
    {
        $this->__member = $member;

        return $this;
    }
}